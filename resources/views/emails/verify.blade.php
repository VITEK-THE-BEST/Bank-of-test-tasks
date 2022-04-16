
<div class="header">
    <div class="header__title">Подтвержение почты вашего аккаунта </div>
</div>
<div class="section">
    <div class="content">
        <div class="content__title">
            Убедитесь, что ваш адрес электронной почты — hello@SmilesDavis.yeah, и что вы указали его при регистрации в CreaTest
        </div>
        <p class="content__description">
            Пожалуйста, нажмите кнопку ниже, чтобы подтвердить свой адрес электронной почты.
        </p>
        <a class="content__verify" href=" {{ route('/email/verify',['header'=>"Authorization: Bearer: {token}"]) }}">Подтвердить</a>
    </div>
</div>
{{--TODO:доделать вызов маршрута в блейде--}}

<style>
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family:monospace

    }
    .header {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        flex-direction: column;
        width: 100%;
        height: 150px;
        z-index: 10;
        background: linear-gradient(180deg, #171b94 0%, #242ace 100%);
        color: #f3f3f3;
    }
    .section {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .header__title {
        margin-left: 40px;
        font-size: 30px;
    }

    .content {
        min-height: 300px;
        margin-top: 60px;
        width: 700px;
        height: 100%;
        padding: 30px;
        border-radius: 25px;
        background-color: #f2f5ff;
        text-align: center;
    }
    .content__title {
        margin-top: 60px;
        font-size: 18px;
        font-weight: 600;

    }
    .content__description {
        font-size: 18px;
        margin-top: 30px;
        margin-bottom: 70px;
        text-align: center;

    }
    .content__verify {
        color: white;
        padding: 16px 90px 16px 90px;
        border-radius: 5px;
        box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
        outline: none;
        font-size: 14px;
        border: none;
        background-color: #2529B4;
        cursor: pointer;
    }
</style>
