<?php

/* @var $this yii\web\View */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'Активные закупки';
?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        setInterval(function () {
            $.pjax.reload({container: '#purchases', async: false});
        }, 30000);
        document.querySelectorAll('.input-settings').forEach(el => el.addEventListener('blur', event => {
            if (el.checkValidity()) {
                let request = new XMLHttpRequest();
                request.open('POST', '/config', true);
                let formData = new FormData()
                formData.append('<?= Yii::$app->request->csrfParam; ?>', '<?= Yii::$app->request->csrfToken; ?>')
                formData.append(el.name, el.value)
                request.send(formData);
            } else
                console.log(el.reportValidity())
        }));
    })
</script>
<div class="row form-inline">
    <div class="col-md-12 form-group ">
        <label for="email1">Email для уведомлений:</label>
        <input name="notification_email" type="email" class="form-control input-settings"
               id="email1" value="<?= $settings['notification_email'] ?>" required>
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

<div class="row">
    <div class="col-xs-12">
        <?php Pjax::begin(['id' => 'purchases']) ?>
        <?= GridView::widget([
            'dataProvider' => $purchaseDataProvider,
            'columns' => [
                [
                    'attribute' => 'number',
                    'label' => '№',
                    'contentOptions' => function ($model, $key, $index, $column) {
                        return ['class' => 'name'];
                    },
                    'content' => function ($data) {
                        return Html::a("#" . $data->number,
                            'https://old.zakupki.mos.ru/#/need/' . $data->number,
                            ['target' => '_blank']);
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
                    'attribute' => 'end_date',
                    'label' => 'Время окончания закупки',
                    'content' => function ($data) {
                        $remainingTime = $data->end_date - time();
                        $content = '<p>' . date('H:i d.m.Y', $data->end_date) . "</p><p>";
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
                        $content .= $data->end_date.'<br>'.time().'<br>'.$remainingTime;
                        return $content;
                    }
                ],
            ],
            'tableOptions' => [
                'class' => 'table table-striped table-bordered table-gridview'
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
