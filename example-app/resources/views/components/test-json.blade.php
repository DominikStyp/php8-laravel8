<?php
 $someArr = ['one', 'two', 'three'];
?>
<div data-arr='<?php echo json_encode($someArr); ?>'
     data-arr2='@json($someArr)'>
    some data
</div>
