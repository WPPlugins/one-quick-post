<?php 

$withcomments = true; //I don't know why, but without this line, the comments template do not load


?>

    <h3 class="oqp-step-title"><?php _e('Comments');?></h3>
    <?php comments_template(); ?>

<?php

?>