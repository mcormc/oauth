<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); $c = Page::getCurrentPage(); if (!$c->isEditMode()) {


$cID = $c->getCollectionID();
$nh = Loader::helper('navigation');
$cPath = $nh->getLinkToCollection($c);

Loader::library('http','client');
Loader::library('oauth','client');

$client = new oauth;
$client->debug = false;
$client->debug_http = true;
$client->server = 'SurveyMonkey';
$client->redirect_uri = $redirect_uri; // https://www.flakr.com/oa/swerve.php
$client->client_id = $client_id; // mcormc
$application_line = __LINE__;
$client->client_secret = $client_secret; // WYUY5NVQ9QSwccVpxd3wnZQRD7ZXkM6g
$client->api_key = $api_key; // mp79b6u97968ek24aa4pn98n

if(strlen($client->client_id) == 0 || strlen($client->client_secret) == 0) die('Please go to SurveyMonkey applications page '.
	'https://developer.surveymonkey.com/apps/register in the API access tab, '.
	'create a new client ID, and in the line '.$application_line.
	' set the client_id to SurveyMonkey user account, client_secret with '.
	'shared secret and api_key with the API key '.
	'The Callback URL must be '.$client->redirect_uri);

$client->scope = '';
if(($success = $client->Initialize())) {
	if(($success = $client->Process())) {
		if(strlen($client->authorization_error)) {
			$client->error = $client->authorization_error;
			$success = false;
		} elseif(strlen($client->access_token)) {
			// $parameters = new stdClass;
			$success = $client->CallAPI(
				'https://api.surveymonkey.net/v2/surveys/get_survey_list?api_key='.$client->api_key,
				'POST', array('fields' => array('title','analysis_url','date_created','date_modified','question_count','num_responses')), array('FailOnAccessError'=>true, 'RequestContentType'=>'application/json'), $result);

		}
	}
	$success = $client->Finalize($success);
}
if($client->exit) exit; ?>

		<div id="OAC<?php  echo $ranID; ?>">
				<?php  if($success) { ?>

					<div>
						<h1><?php  echo 'You have logged in successfully with SurveyMonkey'; ?></h1>
						<?php 
							$data = $result->data->surveys;
							// echo HtmlSpecialChars(print_r($result->data->surveys));
							// echo HtmlSpecialChars(print_r($result->data, 1));
							// print_r($data);

							foreach ($data AS $key => $val) {
								$survey_id = $val->survey_id;
								$survey_title = $val->title;
								$analysis_url = $val->analysis_url;
								$date_created = $val->date_created;
								$date_modified = $val->date_modified;
								$client->CallAPI(
									'https://api.surveymonkey.net/v2/surveys/get_survey_details?api_key='.$client->api_key,
									'POST',
									array('survey_id' => $val->survey_id),
									array('FailOnAccessError'=>true, 'RequestContentType'=>'application/json'),
									$result_2
								);
								$data_2 = $result_2->data;
								?>

								<div class="clearfix"><h3><?php  echo $survey_title; ?></h3><dl class="dl-horizontal">
									<dt><?php  echo t('ID'); ?></dt><dd><?php  echo $survey_id; ?></dd>
									<dt><?php  echo t('Title'); ?></dt><dd><?php  echo $survey_title; ?></dd>
									<dt><?php  echo t('Created'); ?></dt><dd><?php  echo $date_created; ?></dd>
									<dt><?php  echo t('Modified'); ?></dt><dd><?php  echo $date_modified; ?></dd>
									<dt><?php  echo t('Analysis URL'); ?></dt><dd><?php 
										print '<a target="_blank" href="'.$analysis_url.'">View analysis</a>'; ?></dd>
									<dt>Detail</dt><dd><pre><?php  print_r($data_2); ?></pre></dd>
								</dl></div>
							<?php  } ?>


						<!-- redirect_uri - <?php  echo $redirect_uri; ?> -->
						<!-- client_id - <?php  echo $client_id; ?> -->
						<!-- client_secret - <?php  echo $client_secret; ?> -->
						<!-- api_key - <?php  echo $api_key; ?> -->
					</div>
				<?php  } else { ?>

					<div class="alert"><p>Whoops, you broke the Internet.</p></div>
					<pre><?php echo HtmlSpecialChars($client->error); ?></pre>
				<?php  } ?>

		</div>

<?php  } ?>
