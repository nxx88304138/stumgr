<div id="attendance-header" class="page-header">
	<h1>考勤情况</h1>
</div> <!-- /attendance-header -->
<div id="attendance-content" class="section">
	<div class="tabbable">
		<ul class="nav nav-tabs">
		    <li id="view-attendance-nav" class="active"><a href="javascript:void(0)">查看</a></li>
		    <li id="edit-attendance-nav"><a href="javascript:void(0)">编辑</a></li>
		</ul>
	  	<div class="tab-content">
	    	<div id="view-attendance-tab" class="tab-pane active">
	      		<div id="selector">
					<select id="available-years" class="span2">
						<option value="2011">2011-2012学年</option>
						<option value="2012">2012-2013学年</option>
					</select>
					<select id="available-semesters" class="span2">
						<option value="1">第一学期</option>
						<option value="2">第二/三学期</option>
					</select>
					<select id="available-grades" class="span2">
						<option value="2011">2011级</option>
						<option value="2012">2012级</option>
					</select>
					<select id="available-time" class="span2">
						<option value="">最近7天</option>
						<option value="">最近14天</option>
						<option value="">最近一个月</option>
						<option value="">全部</option>
					</select>
				</div>
				<div id="list">
					<table class="table table-hover">
						<thead>
							<tr>
								<td>学号</td>
								<td>姓名</td>
								<td>时间</td>
								<td>情况</td>
								<td>加减分</td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>20116524</td>
								<td>谢浩哲</td>
								<td>2011年10月10日 10:10</td>
								<td>上课迟到</td>
								<td>-0.1</td>
							</tr>
						</tbody>
					</table>
				</div>
	    	</div>
	    	<div id="edit-attendance-tab" class="tab-pane">
	    		
	    	</div>
	 	 </div>
	</div>
</div> <!-- /attendance-section -->

<script type="text/javascript">
    $('#edit-attendance-nav').click(function(){
        $('#view-attendance-nav').removeClass('active');
        $('#view-attendance-tab').removeClass('active');
        $('#edit-attendance-nav').addClass('active');
        $('#edit-attendance-tab').addClass('active');
    });
    $('#view-attendance-nav').click(function(){
        $('#edit-attendance-nav').removeClass('active');
        $('#edit-attendance-tab').removeClass('active');
        $('#view-attendance-nav').addClass('active');
        $('#view-attendance-tab').addClass('active');
    });
</script>