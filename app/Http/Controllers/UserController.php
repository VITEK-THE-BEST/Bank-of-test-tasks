<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            'email' => 'required|unique',
            'password' => 'required',
        ]);

        $CheckUserEmail = User::all('email')->where('email', $request['email']);
        if ($CheckUserEmail->isEmpty()) {
            $validate['password'] = Hash::make($validate['password']);

            $user = User::query()->create($validate);
            $token = $user->createToken($request['email'])->plainTextToken;

            return response()->json(["success" => true,
                "user" => $user,
                "token" => $token], 200);

        }
        return response()->json(["success" => false,
            "error" => "Данный Email уже зарегестрирован"], 409);
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

        return response()->json(["success" => true]);
    }

    /**
     * Обновить данные
     *
     * Обновить данные авторизированного пользователя
     */
    public function update(Request $request)
    {
        $validate = $request->validate([
            'address_id' => 'sometimes',
            'phone' => 'sometimes',
            'name' => 'sometimes',
            'first_name' => 'sometimes',
            'last_name' => 'sometimes',
        ]);

        auth()->user()->update($validate);
        return response()->json(["success" => true]);
    }


    /**
     * Удалить пользователя
     */
    public function delete($id)
    {
        $user = User::query()->find($id);
        $user->delete();
        return response()->json(["success" => true]);
    }
}
