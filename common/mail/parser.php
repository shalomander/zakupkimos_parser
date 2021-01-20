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
        <th>Наименование</th>
        <th>Начальная цена</th>
        <th>Время окончания закупки</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($newPurchases as $purchase): ?>
        <tr>
            <td>
                <?php
                $urlType = $purchase->auction_id ? 'auction' : ($purchase->need_id ? 'need' : 'tenders');
                $urlId = $purchase->auction_id ?: $purchase->need_id ?: $purchase->tender_id;
                $prefix = 'old.';
                switch ($urlType) {
                    case 'auction':
                        $prefix = '';
                        $title = 'Котировочная сессия';
                        break;
                    case 'need':
                        $title = 'Закупка по потребности';
                        $urlType = '#/' . $urlType;
                        break;
                    case 'tenders':
                        $title = 'Конкурентная процедура';
                        $urlType = '#/' . $urlType;
                        break;
                }
                ?>
                <a href="https://<?=$prefix?>zakupki.mos.ru/<?=$urlType?>/<?= $urlId ?>" title="<?=$title?>">
                    #<?= $urlId ?>
                </a>
            </td>
            <td><?= $purchase->name ?></td>
            <td style="text-align: center"><?= number_format($purchase->start_price, 2) ?></td>
            <td style="text-align: center"><?= date('H:i', $purchase->end_date) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>