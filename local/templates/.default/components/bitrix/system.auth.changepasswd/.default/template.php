<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\UI\Extension::load("ui.alerts");

if ($arResult["PHONE_REGISTRATION"]) {
    CJSCore::Init('phone_auth');
}

?>

<div class="authorization_container">

    <?php if(!$arResult["SHOW_FORM"] && !empty($arParams["~AUTH_RESULT"]['TYPE']) && $arParams["~AUTH_RESULT"]['TYPE'] == 'OK') : ?>

        <div class="authorization_form">
            <div class="ui-alert ui-alert-<?= ($arParams["~AUTH_RESULT"]['TYPE'] == 'ERROR') ? 'danger' : 'primary' ?> ui-alert-icon-<?= ($arParams["~AUTH_RESULT"]['TYPE'] == 'ERROR') ? 'danger' : 'info' ?>">
                <span class="ui-alert-message"><?= $arParams["~AUTH_RESULT"]['MESSAGE'] ?></span>
            </div>
            <noindex>
                <a class="link" rel="nofollow"
                   href="<?= $arResult["AUTH_AUTH_URL"] ?>"><?= GetMessage("AUTH_AUTH") ?></a>
            </noindex>
        </div>

    <?php endif;?>

    <? if ($arResult["SHOW_FORM"]): ?>

        <form class="authorization_form js-needs-validation js-submit-onvalid" method="post"
              action="<?= $arResult["AUTH_URL"] ?>" name="bform">
            <h2 class="block_title"><?= GetMessage("AUTH_CHANGE_PASSWORD") ?></h2>

            <?php if ($arParams["~AUTH_RESULT"]) : ?>
                <div class="ui-alert ui-alert-<?= ($arParams["~AUTH_RESULT"]['TYPE'] == 'ERROR') ? 'danger' : 'primary' ?> ui-alert-icon-<?= ($arParams["~AUTH_RESULT"]['TYPE'] == 'ERROR') ? 'danger' : 'info' ?>">
                    <span class="ui-alert-message"><?= $arParams["~AUTH_RESULT"]['MESSAGE'] ?></span>
                </div>
            <?php endif ?>

            <? if ($arResult["BACKURL"] <> ''): ?>
                <input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>"/>
            <? endif ?>
            <input type="hidden" name="AUTH_FORM" value="Y">
            <input type="hidden" name="TYPE" value="CHANGE_PWD">

            <? if ($arResult["PHONE_REGISTRATION"]): ?>
                <div class="input_container">
                    <input type="text" value="<?= htmlspecialcharsbx($arResult["USER_PHONE_NUMBER"]) ?>"
                           class="bx-auth-input" disabled="disabled"
                           placeholder="<? echo GetMessage("sys_auth_chpass_phone_number") ?>"/>
                    <input type="hidden" name="USER_PHONE_NUMBER"
                           value="<?= htmlspecialcharsbx($arResult["USER_PHONE_NUMBER"]) ?>"/>
                </div>
                <? echo GetMessage("sys_auth_chpass_code") ?>
                <input type="text" name="USER_CHECKWORD" maxlength="50" value="<?= $arResult["USER_CHECKWORD"] ?>"
                       class="bx-auth-input" autocomplete="off"/>
            <? else: ?>
                <?php if (empty($arResult["LAST_LOGIN"])) : ?>
                    <div class="input_container">
                        <input type="text" name="USER_LOGIN" maxlength="50" value="<?= $arResult["LAST_LOGIN"] ?>"
                               class="bx-auth-input text_input" placeholder="<?= GetMessage("AUTH_LOGIN") ?>" required/>
                    </div>
                <? else : ?>
                    <input type="hidden" name="USER_LOGIN" maxlength="50" value="<?= $arResult["LAST_LOGIN"] ?>"
                           class="bx-auth-input text_input"/>
                <?php endif; ?>

                <?
                if ($arResult["USE_PASSWORD"]):
                    ?>
                    <div class="input_container">
                        <input type="password" name="USER_CURRENT_PASSWORD" maxlength="255"
                               value="<?= $arResult["USER_CURRENT_PASSWORD"] ?>" class="bx-auth-input text_input"
                               autocomplete="new-password"
                               placeholder="<? echo GetMessage("sys_auth_changr_pass_current_pass") ?>" required/>
                        <span class="label"><? echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"]; ?></span>
                    </div>

                <?
                else:
                    ?>
                    <?php if (empty($arResult["USER_CHECKWORD"])) : ?>
                    <div class="input_container">
                        <input type="text" name="USER_CHECKWORD" maxlength="50"
                               value="<?= $arResult["USER_CHECKWORD"] ?>"
                               class="bx-auth-input text_input" autocomplete="off"
                               placeholder="<?= GetMessage("AUTH_CHECKWORD") ?>" required/>
                    </div>
                <? else: ?>
                    <input type="hidden" name="USER_CHECKWORD" maxlength="50" value="<?= $arResult["USER_CHECKWORD"] ?>"
                           class="bx-auth-input text_input" autocomplete="off"/>
                <?php endif; ?>
                <?
                endif
                ?>
            <? endif ?>
            <div class="input_container">
                <input type="password" name="USER_PASSWORD" maxlength="255" value="<?= $arResult["USER_PASSWORD"] ?>"
                       class="bx-auth-input text_input" autocomplete="new-password"
                       placeholder="<?= GetMessage("AUTH_NEW_PASSWORD_REQ") ?>"
                       required <?= (!empty($arResult['GROUP_POLICY']['PASSWORD_LENGTH']) && intval($arResult['GROUP_POLICY']['PASSWORD_LENGTH']) > 0) ? 'minlength="' . $arResult['GROUP_POLICY']['PASSWORD_LENGTH'] . '"' : '' ?> />
                <span class="label"><? echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"]; ?></span>
            </div>

            <? if ($arResult["SECURE_AUTH"]): ?>
                <span class="bx-auth-secure" id="bx_auth_secure" title="<? echo GetMessage("AUTH_SECURE_NOTE") ?>"
                      style="display:none">
					<div class="bx-auth-secure-icon"></div>
				</span>
                <noscript>
				<span class="bx-auth-secure" title="<? echo GetMessage("AUTH_NONSECURE_NOTE") ?>">
					<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
				</span>
                </noscript>
                <script type="text/javascript">
                    document.getElementById('bx_auth_secure').style.display = 'inline-block';
                </script>
            <? endif ?>
            <div class="input_container">
                <input type="password" name="USER_CONFIRM_PASSWORD" maxlength="255"
                       value="<?= $arResult["USER_CONFIRM_PASSWORD"] ?>" class="bx-auth-input text_input"
                       autocomplete="new-password" placeholder="<?= GetMessage("AUTH_NEW_PASSWORD_CONFIRM") ?>"
                       required/>
            </div>

            <? if ($arResult["USE_CAPTCHA"]): ?>

                <input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>"/>
                <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>" width="180"
                     height="40" alt="CAPTCHA"/>
                <span class="starrequired">*</span><? echo GetMessage("system_auth_captcha") ?></td>
                <input type="text" name="captcha_word" maxlength="50" value="" autocomplete="off"/>
            <? endif ?>

            <div class="flexbox">
                <input class="btn" type="submit" name="change_pwd" value="<?= GetMessage("AUTH_CHANGE") ?>"/>
                <noindex>
                    <a class="link" rel="nofollow"
                       href="<?= $arResult["AUTH_AUTH_URL"] ?>"><?= GetMessage("AUTH_AUTH") ?></a>
                </noindex>
            </div>

        </form>


    <? if ($arResult["PHONE_REGISTRATION"]): ?>

        <script type="text/javascript">
            new BX.PhoneAuth({
                containerId: 'bx_chpass_resend',
                errorContainerId: 'bx_chpass_error',
                interval: <?=$arResult["PHONE_CODE_RESEND_INTERVAL"]?>,
                data:
                <?=CUtil::PhpToJSObject([
                    'signedData' => $arResult["SIGNED_DATA"]
                ])?>,
                onError:
                    function (response) {
                        var errorDiv = BX('bx_chpass_error');
                        var errorNode = BX.findChildByClassName(errorDiv, 'errortext');
                        errorNode.innerHTML = '';
                        for (var i = 0; i < response.errors.length; i++) {
                            errorNode.innerHTML = errorNode.innerHTML + BX.util.htmlspecialchars(response.errors[i].message) + '<br>';
                        }
                        errorDiv.style.display = '';
                    }
            });
        </script>

        <div id="bx_chpass_error" style="display:none"><? ShowError("error") ?></div>

        <div id="bx_chpass_resend"></div>

    <? endif ?>

    <? endif ?>


</div>