<div id="rsm_copy_modal" class="ui mini modal">
    <div class="content" style="min-height: 163.69px;">
        <p>Copy rating scale matrix from previous period?</p>
        <h3 style="width: 100%; text-align: center; border: 1px solid orange; padding: 5px; color: orange; border-radius: 3px;">
            <span class="previous"></span>
            <br />
            <i class="ui icon angle double down"></i>
            <br />
            <span class="new"></span>
        </h3>
    </div>
    <div class="actions">
        <button class="ui small red button deny">Cancel</button>
        <button class="ui small green button approve">Confirm</button>
    </div>
</div>
<div id="rsmCont" style="min-height:500px"></div>
<script>
    $(document).ready(rsmLoad("table"));
</script>