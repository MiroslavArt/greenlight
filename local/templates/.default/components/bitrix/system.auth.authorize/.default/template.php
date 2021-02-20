<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\UI\Extension::load("ui.alerts");

?>


<div class="authorization_container">

    <? if ($arResult["AUTH_SERVICES"]): ?>
        <div class="bx-auth-title"><? echo GetMessage("AUTH_TITLE") ?></div>
    <? endif ?>

    <form name="form_auth" class="authorization_form js-needs-validation js-submit-onvalid" method="post" target="_top" action="<?= $arResult["AUTH_URL"] ?>">

        <h2 class="block_title"><?=GetMessage("AUTH_AUTHORIZATION")?></h2>

        <?php if (!empty($arResult['ERROR_MESSAGE'])) : ?>
            <div class="ui-alert ui-alert-danger ui-alert-icon-danger has-errors">
                <span class="ui-alert-message">
                    <?= $arResult['ERROR_MESSAGE']['']['MESSAGE'] ?>
                </span>
            </div>
        <?php endif ?>
        <?php if ($arParams["~AUTH_RESULT"]) : ?>
            <div class="ui-alert ui-alert-<?=($arParams["~AUTH_RESULT"]['TYPE'] == 'ERROR') ? 'danger' : 'primary'?> ui-alert-icon-<?=($arParams["~AUTH_RESULT"]['TYPE'] == 'ERROR') ? 'danger' : 'info'?>">
                <span class="ui-alert-message"><?=$arParams["~AUTH_RESULT"]['MESSAGE']?></span>
            </div>
        <?php endif ?>

        <input type="hidden" name="AUTH_FORM" value="Y"/>
        <input type="hidden" name="TYPE" value="AUTH"/>
        <? if ($arResult["BACKURL"] <> ''): ?>
            <input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>"/>
        <? endif ?>
        <? foreach ($arResult["POST"] as $key => $value): ?>
            <input type="hidden" name="<?= $key ?>" value="<?= $value ?>"/>
        <? endforeach ?>

        <div class="input_container">
            <input type="text" class="bx-auth-input text_input" placeholder="<?= GetMessage("AUTH_LOGIN") ?>"
                   name="USER_LOGIN" maxlength="255" value="<?= $arResult["LAST_LOGIN"] ?>" required />
        </div><!-- END input_container -->
        <div class="input_container">
            <input type="password" class="bx-auth-input text_input" placeholder="<?= GetMessage("AUTH_PASSWORD") ?>"
                   name="USER_PASSWORD" maxlength="255" autocomplete="off" required />
        </div><!-- END input_container -->
        <? if ($arResult["CAPTCHA_CODE"]): ?>
            <input type="hidden" name="captcha_sid" value="<? echo $arResult["CAPTCHA_CODE"] ?>"/>
            <div class="input_container dbg_captha">
                <label class="label">
                    <? echo GetMessage("AUTH_CAPTCHA_PROMT") ?>
                </label>
                <img src="/bitrix/tools/captcha.php?captcha_sid=<? echo $arResult["CAPTCHA_CODE"] ?>" width="160"
                     height="40" alt="CAPTCHA"/>

                <input type="text" class="text_input" name="captcha_word" maxlength="50" value="" autocomplete="off"/>
            </div>
        <? endif; ?>
        <div class="flexbox">
            <label class="checkbox active"><input type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y"
                                                  checked/><?= GetMessage("AUTH_REMEMBER_ME") ?></label>
            <? if ($arParams["NOT_SHOW_LINKS"] != "Y"): ?>
                <noindex>
                    <a class="link mob" href="<?= $arResult["AUTH_FORGOT_PASSWORD_URL"] ?>"
                       rel="nofollow"><?= GetMessage("AUTH_FORGOT_PASSWORD_2") ?></a>
                </noindex>
            <? endif ?>
        </div><!-- END flexbox -->
        <div class="flexbox">
            <input type="submit" class="btn" name="Login" value="<?= GetMessage("AUTH_AUTHORIZE") ?>"/>
            <? if ($arParams["NOT_SHOW_LINKS"] != "Y"): ?>
                <noindex>
                    <a class="link" href="<?= $arResult["AUTH_FORGOT_PASSWORD_URL"] ?>"
                       rel="nofollow"><?= GetMessage("AUTH_FORGOT_PASSWORD_2") ?></a>
                </noindex>
            <? endif ?>
        </div>

        <? if ($arParams["NOT_SHOW_LINKS"] != "Y" && $arResult["NEW_USER_REGISTRATION"] == "Y" && $arParams["AUTHORIZE_REGISTRATION"] != "Y"): ?>
            <noindex>
                <p>
                    <a href="<?= $arResult["AUTH_REGISTER_URL"] ?>"
                       rel="nofollow"><?= GetMessage("AUTH_REGISTER") ?></a><br/>
                    <?= GetMessage("AUTH_FIRST_ONE") ?>
                </p>
            </noindex>
        <? endif ?>

    </form>
</div>

<script type="text/javascript">
    <?if ($arResult["LAST_LOGIN"] <> ''):?>
    try {
        document.form_auth.USER_PASSWORD.focus();
    } catch (e) {
    }
    <?else:?>
    try {
        document.form_auth.USER_LOGIN.focus();
    } catch (e) {
    }
    <?endif?>
</script>

<? if ($arResult["AUTH_SERVICES"]): ?>
    <?
    $APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "",
        array(
            "AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
            "CURRENT_SERVICE" => $arResult["CURRENT_SERVICE"],
            "AUTH_URL" => $arResult["AUTH_URL"],
            "POST" => $arResult["POST"],
            "SHOW_TITLES" => $arResult["FOR_INTRANET"] ? 'N' : 'Y',
            "FOR_SPLIT" => $arResult["FOR_INTRANET"] ? 'Y' : 'N',
            "AUTH_LINE" => $arResult["FOR_INTRANET"] ? 'N' : 'Y',
        ),
        $component,
        array("HIDE_ICONS" => "Y")
    );
    ?>
<? endif ?>
