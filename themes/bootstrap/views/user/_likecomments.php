<div class="panel-heading <?php echo ($index==0)?'':'mt20'; ?>">
	<p>
		<?php if (Yii::app()->user->id) {
			$saveCount = Save::model()->countByAttributes(array('bp_id'=>$data->comments->bc_id, 'bu_id'=>Yii::app()->user->id, 'type'=>'2'));
			//判断是否是自己发布的
			if ($data->comments->bu_id==Yii::app()->user->id) {
		?>
			<span style="width:13px; color:red;">*</span>
		<?php
			//判断是否是已经收藏的
			}elseif ($saveCount==1) {
		?>
			<span><img src="<?php echo tbu();?>images/s.gif" width="13"></span>
		<?php
			}else{
		?>
			<span><span onclick="getScore('<?php echo $data->comments->bc_id; ?>',this,'2')"><img src="<?php echo tbu();?>images/grayarrow.png" width="13" title="赞"></span></span>
		<?php
			}
		}else{
		?>
			<span><span onclick="getScore('<?php echo $data->comments->bc_id; ?>',this,'2')"><img src="<?php echo tbu();?>images/grayarrow.png" width="13" title="赞"></span></span>
		<?php } ?>
	<span><span><?php echo $data->comments->bc_like; ?></span></span>个赞 
	<?php echo CHtml::link($data->comments->user->bu_name, array('/user/view', 'id'=>$data->comments->bu_id)); ?>
	<?php echo tranTime($data->comments->bc_create_time); ?>
	|	来源：<?php echo CHtml::link($data->comments->posts->bp_title, array('/posts/view', 'id'=>$data->comments->bp_id)); ?>
	</p>
	<p class="fn_b ml5"><?php echo nl2br($data->comments->bc_text); //保留换行?></p>
</div>
