<div class="fade" id="fullDisplaySpinner" ng-show="{{ $show_trigger_name }}"
style="opacity:1; position:fixed; top:0; left:0; height:100%; width:100%; background:rgba(255, 255, 255, 0.7);
z-index: 100; overflow: hidden; -webkit-overflow-scrolling: touch; outline: 0;" >
    <div style="display: block; text-align: center; width: 200px; margin: 0 auto;
    margin-top: 15%; height: 60px; line-height: 60px; z-index: 1000;">
        <span class="glyphicon glyphicon-refresh"></span> Loading...
    </div>
</div>
<style type="text/css">
    #fullDisplaySpinner .glyphicon {
        margin-right: 4px;
        animation: spin infinite 2s linear; }
    @keyframes spin {
        from{ transform:rotate(0deg); }
        to{ transform:rotate(360deg); }
    }
</style>
