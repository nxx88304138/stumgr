<div id="transcripts-header" class="page-header">
	<h1>成绩查询</h1>
</div> <!-- /transcripts-header -->
<div id="transcripts-content" class="section">
	<div id="selector">
		<select id="available-years" class="span2">
			<?php
				foreach ( $available_years as $available_year ) {
					$year = $available_year['school_year'];
					echo '<option value="'.$year.'">'.$year.'-'.($year + 1).'学年</option>';
				}
			?>
		</select>
		<select id="available-semesters" class="span2">
			<option value="1">第一学期</option>
			<option value="2">第二/三学期</option>
		</select>
	</div> <!-- /selector -->
	<div id="list">
		<table id="transcripts-records" class="table table-striped">
			<thead>
				<tr>
					<td>课程代码</td>
					<td>课程名称</td>
					<td>卷面成绩</td>
					<td>最终成绩</td>
					<td>绩点</td>
					<td>排名</td>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div> <!-- /list -->
</div> <!-- /transcripts-content -->