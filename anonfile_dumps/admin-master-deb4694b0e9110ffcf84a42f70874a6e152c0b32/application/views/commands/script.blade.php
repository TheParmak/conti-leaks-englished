<script>
    $('#select_all').click(function(event){
        if(this.checked) {
            $(':checkbox').each(function() {
                this.checked = true;
            });
        }else{
            $(':checkbox').each(function(){
                this.checked = false;
            });
        }
    });
</script>


@if($errors)
    <script type="text/javascript">
        $(function() {
            $('#myModal').modal('show');
            $('.modal-body').css({'max-height': '100%'});
            $('.modal-dialog').css({'height': $('.modal-body').height - 100});
            $('.modal-content').css({'height': $('.modal-body').height - 100});
        });
    </script>
@endif