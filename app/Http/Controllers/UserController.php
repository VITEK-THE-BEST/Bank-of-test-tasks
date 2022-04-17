<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Mail;

/**
 * @authenticated
 * @group Пользователь
 *
 * APIs для юзеров
 */
class UserController extends Controller
{
    /**
     * Регистрация пользователя
     */
    public function register(Request $request)
    {
        $validate = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'patronymic' => 'sometimes',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        $validate['password'] = Hash::make($validate['password']);

        $user = User::query()->create($validate);

        $verify2 = DB::table('password_resets')->where([
            ['email', $validate['email']]
        ]);

        if ($verify2->exists()) {
            $verify2->delete();
        }
        $pin = rand(100000, 999999);
        DB::table('password_resets')
            ->insert(
                [
                    'email' => $validate['email'],
                    'token' => $pin
                ]
            );

        $token = $user->createToken($request['email'])->plainTextToken;

        Mail::to($validate['email'])->send(new VerifyEmail($pin, $user));


        return response()->json(["user" => $user, "token" => $token]);

    }


    /**
     * Верификация email пользователя не должен юзать фронт
     */
    public function verifyEmail($token, $user)
    {
        $user = User::query()->findOrFail($user);

        $select = DB::table('password_resets')
            ->where('email', $user['email'])
            ->where('token', $token);

        if ($select->get()->isEmpty()) {
            return response()->json(["error" => "Неверный ПИН-код"], 404);
        }
        $select->delete();

        $user->email_verified_at = Carbon::now()->getTimestamp();
        $user->save();

        return redirect()->away('http://127.0.0.1:8000');;
    }

    /**
     * Повторрная отправка кода подтверждения
     */
    public function resendPin(Request $request)
    {
        $validate = $request->validate([
            'email' => 'required|email',
        ]);


        $verify = DB::table('password_resets')->where([
            ['email', $validate['email']]
        ]);

        if ($verify->exists()) {
            $verify->delete();
        }

        $token = random_int(100000, 999999);
        DB::table('password_resets')->insert([
            'email' => $validate['email'],
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::to($validate['email'])->send(new VerifyEmail($token));
        return response()->json(['message' => "повторная отправка подтверждения"]);

    }

    /**
     * Авторизация пользователя
     */
    public function getToken(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::query()
            ->where('email', $request['email'])
            ->first();

        if (!$user || !Hash::check($request['password'], $user->password)) {
            return response()->json(["success" => false,
                "error" => "Неверный логин или пароль"], 404);
        }

        $token = $user->createToken($request['email'])->plainTextToken;

        return response()->json(["token" => $token]);
    }

    /**
     * Получение авторизированного пользователя
     */
    public function me()
    {
        $user = auth()->user()->toArray();

        return response()->json(["user" => $user]);
    }

    /**
     * Удалить токен
     *
     * удаляет авторизацию текущего пользователя
     */
    public function dropToken()
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json([]);
    }

    /**
     * Обновить данные
     *
     * Обновить данные авторизированного пользователя
     */
    public function update(Request $request)
    {
        $validate = $request->validate([
            'group' => 'sometimes|exists:groups,name|string',
            'first_name' => 'sometimes',
            'last_name' => 'sometimes',
            'patronymic' => 'sometimes',
            'email' => 'sometimes|unique:users',
        ]);
        if (array_key_exists('group', $validate)) {
            $validate['group_id'] = Group::query()
                ->where('name', $validate['group'])
                ->get()[0]['id'];
        }

        auth()->user()->update($validate);
        return response()->json([]);
    }


    /**
     * Удалить пользователя
     *
     * @urlParam id
     */
    public function delete($id)
    {
        $user = User::query()->find($id);
        $user->delete();
        return response()->json([]);
    }
}
