<div class="alert alert-error hide"><strong>温馨提示: </strong>发生未知错误.</div>
<div id="selector">
	<span>
		<select id="available-grades" class="span2">
			<?php
				foreach ( $available_grades as $available_grade ) {
					$grade = $available_grade['grade'];
					echo '<option value="'.$grade.'">'.$grade.'级</option>';
				}
			?>
		</select>
	</span>
</div> <!-- /selector -->
<div id="list">
	<table id="students-list" class="table table-striped">
		<thead>
			<tr>
				<td>学号</td>
				<td>姓名</td>
				<td>班级</td>
				<td>寝室</td>
				<td>移动电话</td>
				<td>电子邮件</td>
				<td>用户组</td>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div> <!-- /list -->

<!-- Load Students List -->
<script type="text/javascript">
	function get_students_profile_list(grade) {
		$.ajax({
            type: 'POST',
            async: true,
            url: "<?php echo base_url().'admin/get_students_profile_list/'; ?>" + grade,
            dataType: 'JSON',
            success: function(result) {
                if ( result['is_successful'] ) {
                	$('#students-list tbody').empty();
                	var total_students = result['students'].length;
                	for ( var i = 0; i < total_students; ++ i ) {
                		$('#students-list').append(
                			'<tr class="table-datum">' + 
                			'<td>' + result['students'][i]['student_id'] + '</td>' + 
                			'<td>' + result['students'][i]['student_name'] + '</td>' + 
                			'<td>' + result['students'][i]['class'] + '班</td>' + 
                			'<td>' + result['students'][i]['room'] + '</td>' + 
                			'<td>' + result['students'][i]['mobile'] + '</td>' + 
                			'<td>' + result['students'][i]['email'] + '</td>' + 
                			'<td>' + result['students'][i]['user_group'] + '</td>' + 
                			'</tr>'
                		);
                	}
                	set_notice_message(true);
                } else {
                	set_notice_message(false);
                }
            }
        });
	}
</script>
<script type="text/javascript">
	function set_notice_message(is_successful) {
		if ( !is_successful ) {
			$('.alert').css('display', 'block');
		} else {
			$('.alert').css('display', 'none');
		}
		set_footer_position();	// which is defined in index.php
	}
</script>
<script type="text/javascript">
	$(document).ready(function(){
		var grade = $("#available-grades").val();
		get_students_profile_list(grade);
		set_footer_position();
	});
	$("#available-grades").change(function() {
		var grade = $("#available-grades").val();
		return get_students_profile_list(grade);
	});
</script>

<!-- Edit Profile -->
<!-- Profile Modal -->
<div id="profile-dialog" class="modal dialog hide">
 	<div class="modal-header">
    	<button type="button" class="close">×</button>
    	<h3 id="profile-dialog-title">编辑资料</h3>
  	</div>
  	<div class="modal-body">
  		<div id="error-message" class="alert alert-error hide"></div>
    	<table class="table no-border">
    		<tr class="no-border">
    			<td class="text-bold">学号</td>
    			<td class="text-normal"></td>
    			<td class="text-bold">姓名</td>
    			<td><input type="text" name="student_name" maxlength="24"></td>
    		</tr>
    		<tr class="no-border">
    			<td class="text-bold">年级</td>
    			<td>
    				<div class="input-append">
    					<input class="span1" name="grade" type="text" maxlength="4">
    					<span class="add-on">级</span>
    				</div>
    			</td>
    			<td class="text-bold">班级</td>
    			<td>
    				<div class="input-append">
    					<input class="span1" name="class" type="text" maxlength="2">
    					<span class="add-on">班</span>
    				</div>
    			</td>
    		</tr>
    		<tr class="no-border">
    			<td class="text-bold">寝室</td>
    			<td><input type="text" name="room" maxlength="7"></td>
    			<td class="text-bold">用户组</td>
    			<td>
    				<select name="user-group">
    					<?php
    						foreach ( $user_groups as $group ) {
    							echo '<option value="'.$group['group_name'].'">'.$group['display_name'].'</option>';
    						}
    					?>
    				</select>
    			</td>
    		</tr>
    		<tr class="no-border">
    			<td class="text-bold">密码</td>
    			<td><input type="password" name="password"　maxlength="16" /></td>
    		</tr>

    	</table>
  	</div>
  	<div class="modal-footer">
  		<button class="btn btn-danger float-left">删除</button>
		<button id="edit-profile" class="btn btn-primary">确认</button>
		<button class="btn btn-cancel">取消</button>
 	</div>
</div> <!-- /Profile Modal -->

<script type="text/javascript">
	$(document).ready(function(){
	    $('#students-list').delegate('tr.table-datum', 'click', function(){
	        show_profile_dialog($(this).find('td').first().html());
	    });
	    $('.close').click(function(){
			$('#profile-dialog').fadeOut();
		});
		$('.btn-cancel').click(function(){
			$('#profile-dialog').fadeOut();
		});
	});
</script>
<script type="text/javascript">
	function show_profile_dialog(student_id) {
		$('#profile-dialog').fadeIn();
	}
</script>