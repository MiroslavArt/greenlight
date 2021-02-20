<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
\Bitrix\Main\UI\Extension::load("ui.alerts");
?>

<div class="authorization_container">
    <form class="authorization_form js-needs-validation js-submit-onvalid" name="bform" method="post" target="_top" action="<?= $arResult["AUTH_URL"] ?>">
        <h2 class="block_title">Восстановление пароля</h2>

        <?php if ($arParams["~AUTH_RESULT"]) : ?>
            <div class="ui-alert ui-alert-<?=($arParams["~AUTH_RESULT"]['TYPE'] == 'ERROR') ? 'danger' : 'primary'?> ui-alert-icon-<?=($arParams["~AUTH_RESULT"]['TYPE'] == 'ERROR') ? 'danger' : 'info'?>">
                <span class="ui-alert-message"><?=$arParams["~AUTH_RESULT"]['MESSAGE']?></span>
            </div>
        <?php endif ?>

        <?
        if ($arResult["BACKURL"] <> '') {
            ?>
            <input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>"/>
            <?
        }
        ?>
        <input type="hidden" name="AUTH_FORM" value="Y">
        <input type="hidden" name="TYPE" value="SEND_PWD">


        <div class="input_container">
            <input type="text" class="text_input" name="USER_LOGIN" value="<?= $arResult["USER_LOGIN"] ?>"
                   placeholder="<?= GetMessage("sys_forgot_pass_login1") ?>" required/>
            <input type="hidden" name="USER_EMAIL"/>
        </div>
        <div class="label"><? echo GetMessage("sys_forgot_pass_note_email") ?></div>


        <? if ($arResult["PHONE_REGISTRATION"]): ?>

            <div style="margin-top: 16px">
                <div><b><?= GetMessage("sys_forgot_pass_phone") ?></b></div>
                <div><input type="text" name="USER_PHONE_NUMBER" value="<?= $arResult["USER_PHONE_NUMBER"] ?>"/></div>
                <div><? echo GetMessage("sys_forgot_pass_note_phone") ?></div>
            </div>
        <? endif; ?>

        <? if ($arResult["USE_CAPTCHA"]): ?>
            <div style="margin-top: 16px">
                <div>
                    <input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>"/>
                    <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>" width="180"
                         height="40" alt="CAPTCHA"/>
                </div>
                <div><? echo GetMessage("system_auth_captcha") ?></div>
                <div><input type="text" name="captcha_word" maxlength="50" value=""/></div>
            </div>
        <? endif ?>


        <div class="flexbox">
            <input class="btn" type="submit" name="send_account_info" value="<?= GetMessage("AUTH_SEND") ?>"/>

                <noindex>
                    <a class="link" rel="nofollow" href="<?= $arResult["AUTH_AUTH_URL"] ?>"><?= GetMessage("AUTH_AUTH") ?></a>
                </noindex>

        </div>


    </form>


</div>
<script type="text/javascript">
    document.bform.onsubmit = function () {
        document.bform.USER_EMAIL.value = document.bform.USER_LOGIN.value;
    };
    document.bform.USER_LOGIN.focus();
</script>
