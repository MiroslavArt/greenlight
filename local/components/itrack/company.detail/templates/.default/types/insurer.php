<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div class="wrapper">
    <a href="<?=$arParams['LIST_URL']?>" class="back">Вернуться к главному разделу</a>
    <div class="title_container">
        <div class="title_block">
            <span class="type_page">Карточка страховой компании</span>
            <h2 class="block_title"><?= $arResult['COMPANY']['NAME'] ?></h2>
        </div><!-- END title_block -->
        <div class="title_right_block">
            <form class="search_form">
                <input type="text" class="search_text" placeholder="Поиск по списку клиентов"/>
                <input type="submit" class="search" value=""/>
            </form><!-- END search_form -->
            <a href="<?=$arParams['PATH_TO']['useful-documents']?>" class="btn tablet_hide">Полезные документы</a>
            <a href="#" class="btn">Добавить клиента</a>
        </div><!-- END title_right_block -->
    </div><!-- END title_container -->
    <?php if (!empty($arResult['CLIENTS'])) : ?>
        <div id="clients-list">
            <ul class="data_table">
                <li class="row table_head">
                    <div class="table_block clients_column item4"><p>Клиент</p></div>
                    <div class="table_block stat_column item2"><p>Убытки, <br/>шт</p></div>
                    <div class="table_block stat_column item2"><p>Закрыто</p></div>
                    <div class="table_block stat_column item2"><p>Документы <br/>предоставлены</p></div>
                    <div class="table_block stat_column item2"><p>Открыто</p></div>
                    <div class="table_block clients_column item3 align_left"><p>СК (Лидер)</p></div>
                    <div class="table_block links_column item6"><p>Ссылки</p></div>
                </li>
                <li class="row">
                    <div class="table_block clients_column item4" data-name="Клиент">
                        <img src="images/img1.png" width="40" height="40" alt="img1"/>
                        <span>МВМ Entertainment <br/>industries</span>
                    </div><!-- END table_block -->
                    <div class="table_block stat_column item2" data-name="Убытки, шт">3</div>
                    <div class="table_block stat_column green item2" data-name="Закрыто">1</div>
                    <div class="table_block stat_column yellow item2" data-name="Документы предоставлены">1</div>
                    <div class="table_block stat_column red item2" data-name="Открыто">1</div>
                    <div class="table_block clients_column item3 align_left" data-name="СК (Лидер)">
                        <img src="images/img1.png" width="40" height="40" alt="img1"/>
                        <span>ИНГОССТРАХ</span>
                    </div><!-- END table_block -->
                    <div class="table_block links_column item6" data-name="Ссылки">
                        <a href="#" class="link ico_doc"><span>Все СК по договору</span></a>
                        <a href="#" class="link ico_doc"><span>Все убытки</span></a>
                    </div><!-- END table_block -->
                </li>
                <li class="row">
                    <div class="table_block clients_column item4" data-name="Клиент">
                        <img src="images/img2.png" width="40" height="40" alt="img1"/>
                        <span>KRTP</span>
                    </div><!-- END table_block -->
                    <div class="table_block stat_column item2" data-name="Убытки, шт">3</div>
                    <div class="table_block stat_column green item2" data-name="Закрыто">1</div>
                    <div class="table_block stat_column yellow item2" data-name="Документы предоставлены">1</div>
                    <div class="table_block stat_column red item2" data-name="Открыто">1</div>
                    <div class="table_block clients_column item3 align_left" data-name="СК (Лидер)">
                        <img src="images/img1.png" width="40" height="40" alt="img1"/>
                        <span>ИНГОССТРАХ</span>
                    </div><!-- END table_block -->
                    <div class="table_block links_column item6" data-name="Ссылки">
                        <a href="#" class="link ico_doc"><span>Все СК по договору</span></a>
                        <a href="#" class="link ico_doc"><span>Все убытки</span></a>
                    </div><!-- END table_block -->
                </li>
                <li class="row">
                    <div class="table_block clients_column item4" data-name="Клиент">
                        <img src="images/img3.png" width="40" height="40" alt="img1"/>
                        <span>Untropetion Ltd</span>
                    </div><!-- END table_block -->
                    <div class="table_block stat_column item2" data-name="Убытки, шт">3</div>
                    <div class="table_block stat_column green item2" data-name="Закрыто">1</div>
                    <div class="table_block stat_column yellow item2" data-name="Документы предоставлены">1</div>
                    <div class="table_block stat_column red item2" data-name="Открыто">1</div>
                    <div class="table_block clients_column item3 align_left" data-name="СК (Лидер)">
                        <img src="images/img1.png" width="40" height="40" alt="img1"/>
                        <span>ИНГОССТРАХ</span>
                    </div><!-- END table_block -->
                    <div class="table_block links_column item6" data-name="Ссылки">
                        <a href="#" class="link ico_doc"><span>Все СК по договору</span></a>
                        <a href="#" class="link ico_doc"><span>Все убытки</span></a>
                    </div><!-- END table_block -->
                </li>
                <li class="row">
                    <div class="table_block clients_column item4" data-name="Клиент">
                        <img src="images/img4.png" width="40" height="40" alt="img1"/>
                        <span>Eoroppeskus RDF</span>
                    </div><!-- END table_block -->
                    <div class="table_block stat_column item2" data-name="Убытки, шт">3</div>
                    <div class="table_block stat_column green item2" data-name="Закрыто">1</div>
                    <div class="table_block stat_column yellow item2" data-name="Документы предоставлены">1</div>
                    <div class="table_block stat_column red item2" data-name="Открыто">1</div>
                    <div class="table_block clients_column item3 align_left" data-name="СК (Лидер)">
                        <img src="images/img1.png" width="40" height="40" alt="img1"/>
                        <span>ИНГОССТРАХ</span>
                    </div><!-- END table_block -->
                    <div class="table_block links_column item6" data-name="Ссылки">
                        <a href="#" class="link ico_doc"><span>Все СК по договору</span></a>
                        <a href="#" class="link ico_doc"><span>Все убытки</span></a>
                    </div><!-- END table_block -->
                </li>
                <li class="row">
                    <div class="table_block clients_column item4" data-name="Клиент">
                        <img src="images/img5.png" width="40" height="40" alt="img1"/>
                        <span>Tapstar Industries Professional  for <br/>Cunsommer Parts</span>
                    </div><!-- END table_block -->
                    <div class="table_block stat_column item2" data-name="Убытки, шт">3</div>
                    <div class="table_block stat_column green item2" data-name="Закрыто">1</div>
                    <div class="table_block stat_column yellow item2" data-name="Документы предоставлены">1</div>
                    <div class="table_block stat_column red item2" data-name="Открыто">1</div>
                    <div class="table_block clients_column item3 align_left" data-name="СК (Лидер)">
                        <img src="images/img1.png" width="40" height="40" alt="img1"/>
                        <span>ИНГОССТРАХ</span>
                    </div><!-- END table_block -->
                    <div class="table_block links_column item6" data-name="Ссылки">
                        <a href="#" class="link ico_doc"><span>Все СК по договору</span></a>
                        <a href="#" class="link ico_doc"><span>Все убытки</span></a>
                    </div><!-- END table_block -->
                </li>
                <li class="row">
                    <div class="table_block clients_column item4" data-name="Клиент">
                        <img src="images/img6.png" width="40" height="40" alt="img1"/>
                        <span>Imagination Public</span>
                    </div><!-- END table_block -->
                    <div class="table_block stat_column item2" data-name="Убытки, шт">3</div>
                    <div class="table_block stat_column green item2" data-name="Закрыто">1</div>
                    <div class="table_block stat_column yellow item2" data-name="Документы предоставлены">1</div>
                    <div class="table_block stat_column red item2" data-name="Открыто">1</div>
                    <div class="table_block clients_column item3 align_left" data-name="СК (Лидер)">
                        <img src="images/img1.png" width="40" height="40" alt="img1"/>
                        <span>ИНГОССТРАХ</span>
                    </div><!-- END table_block -->
                    <div class="table_block links_column item6" data-name="Ссылки">
                        <a href="#" class="link ico_doc"><span>Все СК по договору</span></a>
                        <a href="#" class="link ico_doc"><span>Все убытки</span></a>
                    </div><!-- END table_block -->
                </li>
            </ul><!-- END data_table -->
            <ul class="data_table no_bg">
                <li class="row">
                    <div class="table_block head_column item4" data-name="Клиент"><p>Итого</p></div>
                    <div class="table_block stat_column item2" data-name="Убытки, шт">18</div>
                    <div class="table_block stat_column item2" data-name="Закрыто">6</div>
                    <div class="table_block stat_column item2" data-name="Документы предоставлены">6</div>
                    <div class="table_block stat_column item2" data-name="Открыто">6</div>
                    <div class="table_block links_column"></div>
                </li>
            </ul><!-- END data_table -->
        </div>
    <?php else: ?>
        <div id="clients-list">
            <div class="ui-alert ui-alert-danger ui-alert-icon-danger has-errors">
                <span class="ui-alert-message">Данные не найдены</span>
            </div>
        </div>
    <?php endif; ?>
</div><!-- END wrapper -->