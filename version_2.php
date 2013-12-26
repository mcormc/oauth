<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); if (!Page::getCurrentPage()->isEditMode()) {

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
if($client->exit) exit;

if($success) {

	// MONKEY
	Loader::library('monkey','client');

	// BASIC USAGE
	// $m = new Monkey($api_key, $access_token);
	// $result = $m->getSurveyList();
	// if ($result["success"]) print_r($result["data"]["surveys"]);
	// else print_r($result["message"]);

?>

			<h1><?php  echo Page::getCurrentPage()->getCollectionName(); ?></h1>
			<div class="clearfix"><?php  $data = $result->data->surveys; foreach ($data AS $key => $val) {

					$survey_id = $val->survey_id;
					$survey_title = $val->title;
					$analysis_url = $val->analysis_url;
					$date_created = $val->date_created;
					$date_modified = $val->date_modified; ?>

				<h3><?php echo $survey_title; ?></h3><br>
				<?php 
				// $m = new Monkey("myApiKey", "myAccessToken");
				$m = new Monkey($api_key, $client->access_token);
				// $result = $m->getSurveyList();
				$result = $m->getCollectorList();
				if ($result["success"]) print_r($result["data"]["surveys"]);
				else print_r('<div>'.$result["message"].'</div>');
				?><br><br>

			<?php  } ?>

			</div>
<?php  } else { ?>

			<div class="alert"><p>Whoops, you broke the Internet.</p></div>
			<pre><?php echo HtmlSpecialChars($client->error); ?></pre>
<?php  } ?>

<?php  } ?>
