<?php

/* @var $this yii\web\View */

use common\models\Status;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'Активные закупки';
$gridViewTableClasses = '';
$gridViewTableClasses .= (isset($settings['hide_column-3']) and $settings['hide_column-3'] == 'true') ? ' hide_column-3' : '';
$gridViewTableClasses .= (isset($settings['hide_column-6']) and $settings['hide_column-6'] == 'true') ? ' hide_column-6' : '';
?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        setInterval(function () {
            $.pjax.reload({container: '#purchases', async: false});
        }, 20000);
        document.querySelectorAll('.input-settings').forEach(el => el.addEventListener('change', event => {
            setConfig(event)
        }))
        document.querySelectorAll('.input-period').forEach(el => el.addEventListener('change', event => {
            $.pjax.reload({container: '#purchases', async: false});
        }))
        document.querySelectorAll('.gridview').forEach(el => el.addEventListener('change', event => {
            if (event.target.classList.contains('input-status'))
                setStatus(event)
        }))
        document.querySelectorAll('.column-control').forEach(el => el.addEventListener('change', event => {
            toggleColumn(event)
        }))
    })

    function setConfig(e) {
        let el = e.target;
        if (el.checkValidity()) {
            let request = new XMLHttpRequest();
            request.open('POST', 'config', true);
            let formData = new FormData()
            formData.append('<?= Yii::$app->request->csrfParam; ?>', '<?= Yii::$app->request->csrfToken; ?>')
            if (el.type == "checkbox")
                formData.append(el.name, el.checked)
            else
                formData.append(el.name, el.value)
            request.send(formData);
        } else
            console.log(el.reportValidity())
    }

    function setStatus(e) {
        let el = e.target;
        let request = new XMLHttpRequest();
        request.open('POST', 'status', true);
        let formData = new FormData()
        formData.append('<?= Yii::$app->request->csrfParam; ?>', '<?= Yii::$app->request->csrfToken; ?>')
        formData.append(el.name, el.value)
        formData.append('id', el.dataset.id)
        request.send(formData);
        if (el.value > 1) {
            el.classList.add('status-viewed')
            el.classList.remove('status-not_viewed')
        } else {
            el.classList.add('status-not_viewed')
            el.classList.remove('status-viewed')
        }
    }

    function toggleColumn(e) {
        let el = e.target;
        let col = el.dataset.column;
        let gridViewTables = document.querySelectorAll('.table-gridview');
        gridViewTables.forEach(el => el.classList.toggle('hide_column-' + col))
        console.log(col)
    }
</script>
<div class="row form-inline form-settings">
    <div class="col-md-6 form-group ">
        <div class="form-label">
            Email для уведомлений:
        </div>
        <div>
            <input name="notification_email" type="email" class="form-control input-settings"
                   id="email1" value="<?= $settings['notification_email'] ?>" required>
        </div>
        <div class="form-label">
            Отображать закупки за:
        </div>
        <div>
            <?= Html::dropDownList('show_purchases_period',
                $settings['show_purchases_period'],
                [
                    '-1 day' => 'День',
                    '-1 week' => 'Неделя',
                    '-1 month' => 'Месяц',
                    '-1 year' => 'Год',
                ],
                [
                    'class' => 'form-control input-settings input-period'
                ]
            );
            ?>
        </div>
    </div>
    <div class="col-md-6 form-group ">
        <div class="form-label">
            Скрыть столбцы:
        </div>
        <div>
            <input name="hide_column-3" type="checkbox" class="form-control input-settings column-control"
                   id="checkbox1" data-column="3"
                <?= (isset($settings['hide_column-3']) and $settings['hide_column-3'] == 'true') ? 'checked' : '' ?>>
            <label for="checkbox1">Заказчик</label>
        </div>
        <div>
            <input name="hide_column-6" type="checkbox" class="form-control input-settings column-control"
                   id="checkbox2" data-column="6"
                <?= (isset($settings['hide_column-6']) and $settings['hide_column-6'] == 'true') ? 'checked' : '' ?>>
            <label for="checkbox2">Адрес поставки</label>
        </div>

    </div>
    <!--div class="col-md-6 form-group">
        <label for="time1">Не отправлять уведомления с </label>
        <input name="silent_from" type="time" class="form-control input-settings"
               id="time1" value="<? //= $settings['silent_from'] ?>" required>
        <label for="time2">до </label>
        <input name="silent_till" type="time" class="form-control input-settings"
               id="time2" value="<? //= $settings['silent_till'] ?>" required>
    </div-->
</div>

<div class="row gridview">
    <div class="col-xs-12">
        <?php Pjax::begin(['id' => 'purchases']) ?>
        <?= GridView::widget([
            'dataProvider' => $purchaseDataProvider,
            'columns' => [
                [
                    'attribute' => 'status_id',
                    'label' => 'Статус',
                    'contentOptions' => function ($model, $key, $index, $column) {
                        return ['class' => 'va-middle text-center'];
                    },
                    'content' => function ($data) {

//                        $html = '<div class="form-check text-center">';
//                        $html.=Html::checkbox('status_id', false, ['class'=>'form-check-input']);
//                        $html.='</div>';
//                        return $html;
                        $statuses = Status::find()->asArray()->all();
                        $statusColorClass = ($data->status_id > 1) ? 'status-viewed' : 'status-not_viewed';
                        return Html::dropDownList('status_id',
                            $data->status_id,
                            ArrayHelper::map($statuses, 'id', 'name'),
                            [
                                'data-id' => $data->id,
                                'class' => 'input-status form-control ' . $statusColorClass
                            ]
                        );
                    }
                ],
                [
                    'attribute' => 'number',
                    'label' => '№',
                    'contentOptions' => function ($model, $key, $index, $column) {
                        return ['class' => 'name'];
                    },
                    'content' => function ($data) {
                        $urlType = $data->auction_id ? 'auction' : ($data->need_id ? 'need' : 'tenders');
                        $urlId = $data->auction_id ?: $data->need_id ?: $data->tender_id;
                        switch ($urlType) {
                            case 'auction':
                                $prefix = '';
                                $title = 'Котировочная сессия';
                                break;
                            case 'need':
                                $title = 'Закупка по потребности';
                                $prefix = 'old.';
                                $urlType = '#/' . $urlType;
                                break;
                            case 'tenders':
                                $title = 'Конкурентная процедура';
                                $prefix = 'old.';
                                $urlType = '#/' . $urlType;
                                break;
                        }
                        return Html::a("#" . $data->number,
                            "https://{$prefix}zakupki.mos.ru/{$urlType}/{$urlId}",
                            ['target' => '_blank',
                                'title' => $title]);
                    }
                ],
                [
                    'attribute' => 'purchase_creator_name',
                    'label' => 'Заказчик',
                ],
                [
                    'attribute' => 'name',
                    'label' => 'Наименование',
                ],
                [
                    'attribute' => 'start_price',
                    'label' => 'Начальная цена',
                    'contentOptions' => function ($model, $key, $index, $column) {
                        return ['class' => 'price'];
                    },
                    'content' => function ($data) {
                        return number_format($data->start_price, 2);
                    }
                ],
                [
                    'attribute' => 'delivery_place',
                    'label' => 'Адрес поставки',
                ],
                [
                    'attribute' => 'rabs',
                    'label' => 'Время окончания закупки',
                    'content' => function ($data) {
                        $remainingTime = $data->end_date - time();
                        $content = '<p>' . Yii::$app->formatter->asDate($data->end_date) . "</p><p>";
                        if ($remainingTime <= 0) {
                            $content .= '(завершена)';
                        } else if ($remainingTime <= 60) {
                            $content .= '(до конца <1 мин)';
                        } else {
                            $remainingHours = intdiv($remainingTime, 3600);
                            $remainingMinutes = intdiv(($remainingTime - $remainingHours * 3600), 60);
                            $content .= '(до конца ';
                            $content .= $remainingHours . 'ч ';
                            $content .= $remainingMinutes . 'мин)';
                        }
                        $content .= '</p>';
//                      $content .= $data->end_date.'<br>'.time().'<br>'.$remainingTime;
                        return $content;
                    }
                ]
            ],
            'tableOptions' => [
                'class' => 'table table-striped table-bordered table-gridview' . $gridViewTableClasses
            ],
            'rowOptions' => function ($data) {
                if ($data->end_date - time() < 0) {
                    return ['class' => 'purchase-closed'];
                }
            },
        ]);
        ?>
        <?php Pjax::end() ?>
    </div>
</div>
