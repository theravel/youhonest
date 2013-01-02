<html>
    <body>

        <input type="hidden" id="youhonest_return_url" value="<?=$url?>" />

        <script type="text/javascript">
            setTimeout(function(){
                window.location = document.getElementById('youhonest_return_url').value;
            }, 300);
        </script>

    </body>
</html>