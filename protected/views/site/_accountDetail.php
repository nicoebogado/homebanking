<?php

switch ($data['accountType']) {
    case 'AH':
        $icon = 'drawer';
        break;
    case 'TJ':
        $icon = 'credit-card';
        break;
    case 'PT':
        $icon = 'briefcase';
        break;
    case 'CC':
        $icon = 'cc';
        break;

    default:
        $icon = null;
        break;
}
?>
<div class="list-group">
    <a class="list-group-item" href="<?php echo Yii::app()->createUrl("/report/accountDetails", array("id" => $data["hash"])) ?>">
        <table class="wd-wide">
            <tbody>
                <tr>
                    <td class="wd-xs hidden-xs hidden-sm">
                        <div class="ph">
                            <em class="icon-<?php echo $icon ?> fa-2x"></em>
                        </div>
                    </td>
                    <td>
                        <div class="ph">
                            <h4 class="media-box-heading"><?php echo $data['accountTypeDesc'] ?></h4>
                            <small class="text-muted">
                                <?php if (isset($data['accountType']) && $data['accountType'] != 'TJ') : ?>
                                    <?php echo 'N° ' .
                                        ((Yii::app()->params['maskedAccountNumber'] == 'N') ?
                                            $data['accountNumber'] :
                                            $data['maskedAccountNumber']) . ' - ' . $data['denomination']
                                    ?>
                                <?php else : ?>
                                    <?php echo 'N° ' . $data['maskedCreditCardNumber'] . ' - ' . $data['denomination'] ?>
                                <?php endif; ?>
                            </small>
                        </div>
                    </td>
                    <td class="wd-sm">
                        <div class="ph">
                            <?php $this->widget('booster.widgets.TbButtonGroup', array(
                                'size' => 'large',
                                'buttons' => array(
                                    array(
                                        'icon' => 'fa fa-chevron-down',
                                        'items' => array(
                                            array(
                                                'label' => Yii::t('accountSummary', 'Detalles'),
                                                'icon'  => 'icon-list',
                                                'url'   => array(
                                                    "/report/accountDetails",
                                                    "id" => $data["hash"],
                                                ),
                                            ),
                                            in_array($data['accountType'], array('AH', 'AP', 'CC', 'TJ')) ? array(
                                                'label' => Yii::t('accountSummary', 'Movimientos'),
                                                'icon'  => 'icon-loop',
                                                'url'   => array(
                                                    "/report/movements",
                                                    "id" => $data["hash"],
                                                ),
                                            ) : array(),
                                            in_array($data['accountType'], array('AH')) ? array(
                                                'label' => Yii::t('accountSummary', 'Extracto'),
                                                'icon'  => 'icon-loop',
                                                'url'   => array(
                                                    "/report/accountExtract",
                                                    "accountHash" => $data["hash"],
                                                ),
                                            ) : array(),
                                            in_array($data['accountType'], array('AH', 'CC')) ? array(
                                                'label' => Yii::t('returnedChecks', 'Cheques Devueltos'),
                                                'icon'  => 'icon-action-undo',
                                                'url'   => array(
                                                    "/report/returnedChecks",
                                                    "id" => $data["hash"],
                                                ),
                                            ) : array(),
                                            ($data['accountType'] === 'PT') ? array(
                                                'label' => Yii::t('accountSummary', 'Cuotas'),
                                                'icon'  => 'icon-layers',
                                                'url'   => array(
                                                    "/payments/loan",
                                                    "id" => $data["hash"],
                                                ),
                                            ) : array(),
                                            ($data['accountType'] === 'AP') ? array(
                                                'label' => Yii::t('accountSummary', 'Cupones'),
                                                'icon'  => 'icon-layers',
                                                'url'   => array(
                                                    "/report/cdaCoupons",
                                                    "id" => $data["hash"],
                                                ),
                                            ) : array(),
                                            ($data['accountType'] === 'TJ') ? array(
                                                'label' => Yii::t('accountSummary', 'Tarjetas Adicionales'),
                                                'icon'  => 'icon-credit-card',
                                                'url'   => array(
                                                    "/report/aditionalCards",
                                                    "id" => $data["hash"],
                                                ),
                                            ) : array(),
                                            ($data['accountType'] === 'CC') ? array(
                                                'label' => Yii::t('accountSummary', 'Chequeras'),
                                                'icon'  => 'icon-book-open',
                                                'url'   => array(
                                                    "/report/checkbooks",
                                                    "id" => $data["hash"],
                                                ),
                                            ) : array(),
                                        ),
                                    ),
                                ),
                            )); ?>
                        </div>
                    </td>
                    <td class="wd-sd hidden-xs hidden-sm text-right">
                        <div class="ph">
                            <h4>
                                <span data-toggle="tooltip" data-original-title="<?php if ($data['accountType'] === 'AH') {
                                                                                        echo 'Saldo disponible';
                                                                                    } elseif ($data['accountType'] === 'PT') {
                                                                                        echo 'Saldo Capital';
                                                                                    } elseif ($data['accountType'] === 'TJ') {
                                                                                        echo 'Saldo disponible';
                                                                                    } ?>">
                                    <?php echo $data['currency'] . ' ' . Yii::app()->numberFormatter->formatDecimal($data["credit"]); ?>
                                    <em class="fa fa-money mr" style="margin:0 0 0 0 5px"></em>
                                </span>
                            </h4>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </a>
</div>