<?php
$this->registerCss("
    table{
        border-collapse: collapse;
    }
    table, th, td{
        border: #2d2d2d 1px solid
    }
    h2{
        text-align: center;
        margin: 1rem;
    }
    th{
        text-align: center;
    }
    th, td{
        padding: .5rem;
    }
");
?>
<h2>На сайте появились новые закупки:</h2>
<table>
    <thead>
    <tr>
        <th>№</th>
        <th>Заказчик</th>
        <th>Наименование</th>
        <th>Начальная цена</th>
        <th>Адрес поставки</th>
        <th>Время окончания закупки</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($newPurchases as $purchase): ?>
        <tr>
            <td>
                <a href="https://old.zakupki.mos.ru/#/need/<?= $purchase->number ?>">
                    #<?= $purchase->number ?>
                </a>
            </td>
            <td><?= $purchase->purchase_creator_name ?></td>
            <td><?= $purchase->name ?></td>
            <td style="text-align: center"><?= number_format($purchase->start_price, 2) ?></td>
            <td><?= $purchase->delivery_place ?></td>
            <td style="text-align: center"><?= date('H:i', $purchase->end_date) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>