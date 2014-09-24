<?php
if (isset($div_main_id) && $div_main_id != "") {
    print "</div> <!-- end of div main-->";
} else { ?>
</div> <!--end of div main-->
</div> <!--end of div main_panel-->
<?php } ?>

</center>

<p><br></p>
<p><br></p>


<div class="footer">
<p>Powered by <a href="../about/">&Pi;BB</a> <br/>Copyright &copy; 2013-2014</p>
</div>

<?php 
if ($_BBS_JIA_THIS_THREAD) { include_once("../theme/share.php"); } 
if ($_BBS_INCLUDE_ANALYTICS) { include_once("../js/analytics.php"); }
?>
</body>
</html>
