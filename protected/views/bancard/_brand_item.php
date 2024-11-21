<div
    class="col-xs-6 col-sm-4 col-md-3 col-lg-2 filtr-item"
    data-category="<?= $brand->categories ?>"
    data-toggle="modal"
    data-target="#brand-modal"
    data-brand="<?= $this->_encodeBrand($brand); ?>"
>
    <div class="panel panel-default">
        <div class="panel-body">
            <img class="img-responsive" src="https://www.bancard.com.py/s4/public/billing_brands_logos/<?= $brand->logo_resource_id ?>.normal.png" alt="<?= $brand->name?> logo">
        </div>
        <div class="panel-footer">
            <span class="item-desc"><?= $brand->name?></span>
        </div>
    </div>
</div>
