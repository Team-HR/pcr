<div id="iMatrixCont" style="min-height:500px">
    <div>
        <h1 class="ui center aligned icon header">
            <i class="eye icon"></i>
            <div class="content">
                Individual Rating Scale Matrix
                <div class="sub header">Show/Edit Individual Rating scale Matrix</div>
            </div>
        </h1>
    </div>
    <br>
    <br>
    <div style="margin-left:30%">
        <form class="ui form noSubmit">
            <div class="two fields">
                <div class="ui four wide field">
                    <select id="period">
                        <option value="January - June">January - June</option>
                        <option value="July - December">July - December</option>
                    </select>
                </div>
                <div class="ui four wide field">
                    <select id="year">
                        <?= $year->get_year() ?>
                    </select>
                </div>
                <div class="ui four wide field">
                    <div class="ui submit button" onclick="iMatrix_period(this)">Go</div>
                </div>
            </div>
        </form>
    </div>
</div>