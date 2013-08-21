<div id="peer-assessment-header" class="page-header">
	<h1>学生互评</h1>
</div> <!-- /peer-assessment-header -->
<div id="peer-assessment-content" class="section">
	<div class="alert alert-info">
		<strong>Heads up!</strong>
		Options for individual popovers can alternatively be specified through the use of data attributes.
	</div>
	<table class="table table-striped">
		<thead>
			<tr>
				<td rowspan="2">学号</td>
				<td rowspan="2">姓名</td>
				<td colspan="4" class="left-border text-center">道德</td>
				<td colspan="4" class="left-border text-center">体育</td>
				<td colspan="4" class="left-border text-center">能力</td>
			</tr>
			<tr>
				<td class="left-border">优</td>
				<td>良</td>
				<td>中</td>
				<td>差</td>
				<td class="left-border">优</td>
				<td>良</td>
				<td>中</td>
				<td>差</td>
				<td class="left-border">优</td>
				<td>良</td>
				<td>中</td>
				<td>差</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>20116524</td>
				<td>谢浩哲</td>
				<td class="left-border"><input type="radio" name="moral-20116524" class="moral excellent" value="excellent" /></td>
				<td><input type="radio" class="moral good" name="moral-20116524" value="good" checked /></td>
				<td><input type="radio" class="moral medium" name="moral-20116524" value="medium" /></td>
				<td><input type="radio" class="moral poor" name="moral-20116524" value="poor" /></td>
				<td class="left-border"><input type="radio" name="sport-20116524" class="sport excellent" value="excellent" /></td>
				<td><input type="radio" class="sport good" name="sport-20116524" value="good" checked /></td>
				<td><input type="radio" class="sport medium" name="sport-20116524" value="medium" /></td>
				<td><input type="radio" class="sport poor" name="sport-20116524" value="poor" /></td>
				<td class="left-border"><input type="radio" name="ability-20116524" class="ability excellent" value="excellent" /></td>
				<td><input type="radio" class="ability good" name="ability-20116524" value="good" checked /></td>
				<td><input type="radio" class="ability medium" name="ability-20116524" value="medium" /></td>
				<td><input type="radio" class="ability poor" name="ablility-20116524" value="poor" /></td>
			</tr>
		</tbody>
	</table>
	<button id="submit" class="btn btn-primary">提交</button>
  	<button class="btn btn-cancel">重置</button>
</div> <!-- /peer-assessment-content -->

<script type="text/javascript">
	$._messengerDefaults = {
		extraClasses: 'messenger-fixed messenger-theme-future messenger-on-bottom messenger-on-right'
	}
	$('input[type="radio"]').click(function(){
		var moral_votes = new Array(4),
		    sport_votes = new Array(4),
		    ability_votes = new Array(4);
		moral_votes[0] = $('.moral.excellent:checked').length;		moral_votes[1] = $('.moral.good:checked').length;
		moral_votes[2] = $('.moral.medium:checked').length;			moral_votes[3] = $('.moral.poor:checked').length;
		sport_votes[0] = $('.sport.excellent:checked').length;		sport_votes[1] = $('.sport.good:checked').length;
		sport_votes[2] = $('.sport.medium:checked').length;			sport_votes[3] = $('.sport.poor:checked').length;
		ability_votes[0] = $('.ability.excellent:checked').length;	ability_votes[1] = $('.ability.good:checked').length;
		ability_votes[2] = $('.ability.medium:checked').length;		ability_votes[3] = $('.ability.poor:checked').length;
		$.globalMessenger().post({
		  message: "当前选中人数:<br />" + 
				   "[道德] 优: " + moral_votes[0]   + "人, 良: " + moral_votes[1]  + 
				      "人, 中: " + moral_votes[2]   + "人, 差: " + moral_votes[3] + "人<br />" +
				   "[体育] 优: " + sport_votes[0]   + "人, 良: " + sport_votes[1]  + 
				      "人, 中: " + sport_votes[2]   + "人, 差: " + sport_votes[3] + "人<br />" +
			  	   "[能力] 优: " + ability_votes[0] + "人, 良: " + ability_votes[1]  + 
				      "人, 中: " + ability_votes[2] + "人, 差: " + ability_votes[3] + "人<br />",
		  type: 'error',
		  showCloseButton: true
		});
	});
</script>