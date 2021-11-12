<?php
/**
 * @package TutorLMS/Templates
 * @version 1.5.7
 */


global $post;
$currentPost = $post;
$course = tutor_utils()->get_course_by_quiz(get_the_ID());
$quiz_id = get_the_ID();
$is_started_quiz = tutor_utils()->is_started_quiz();

$previous_attempts = tutor_utils()->quiz_attempts();
$attempted_count = is_array($previous_attempts) ? count($previous_attempts) : 0;
$questions_order = tutor_utils()->get_quiz_option($quiz_id, 'questions_order', 'rand');
$attempts_allowed = tutor_utils()->get_quiz_option($quiz_id, 'attempts_allowed', 0);
$passing_grade = tutor_utils()->get_quiz_option($quiz_id, 'passing_grade', 0);
$feedback_mode = tutor_utils()->get_quiz_option($quiz_id, 'feedback_mode', 0);

$attempt_remaining = $attempts_allowed - $attempted_count;
$quiz_answers = array();
?>

<div id="tutor-quiz-image-matching-choice" class="tutor-quiz-wrap tutor-quiz-wrap-<?php the_ID(); ?>">
	<?php

    do_action('tutor_quiz/body/before', $quiz_id);

	if ($is_started_quiz){
		$quiz_attempt_info = tutor_utils()->quiz_attempt_info($is_started_quiz->attempt_info);
		$quiz_attempt_info['date_time_now'] = date("Y-m-d H:i:s", tutor_time());

		$time_limit_seconds = tutor_utils()->avalue_dot('time_limit.time_limit_seconds', $quiz_attempt_info);
		$question_layout_view = tutor_utils()->get_quiz_option($quiz_id, 'question_layout_view');
		!$question_layout_view ? $question_layout_view = 'single_question' : 0;

		$hide_quiz_time_display = (bool) tutor_utils()->avalue_dot('hide_quiz_time_display', $quiz_attempt_info);
		$hide_question_number_overview = (bool) tutor_utils()->avalue_dot('hide_question_number_overview', $quiz_attempt_info);
		$hide_previous_button = (bool) tutor_utils()->get_option('quiz_previous_button_disabled', false);

		$remaining_time_secs = (strtotime($is_started_quiz->attempt_started_at) + $time_limit_seconds ) - strtotime($quiz_attempt_info['date_time_now']);

		$remaining_time_context = tutor_utils()->seconds_to_time_context($remaining_time_secs);
		$questions = tutor_utils()->get_random_questions_by_quiz();
	?>
		
		<!-- Quiz Meta -->
		<?php
			include 'parts/meta.php';
		?>

		<!-- Quiz Question & Answer -->
		<?php
			if (is_array($questions) && count($questions)) {
				include 'parts/question.php';
		?>
		

		<?php
			} else{
		?>
            <div class="start-quiz-wrap">
                <form id="tutor-finish-quiz" method="post">
					<?php wp_nonce_field( tutor()->nonce_action, tutor()->nonce ); ?>

                    <input type="hidden" value="<?php echo $quiz_id; ?>" name="quiz_id"/>
                    <input type="hidden" value="tutor_finish_quiz_attempt" name="tutor_action"/>

                    <button type="submit" class="tutor-btn" name="finish_quiz_btn" value="finish_quiz">
                        <i class="icon-floppy"></i> <?php _e( 'Finish', 'tutor' ); ?>
                    </button>
                </form>
            </div>

			<?php
		}
	}else{

		



		if ($previous_attempts){
			do_action('tutor_quiz/previous_attempts_html/before', $previous_attempts, $quiz_id);

			ob_start();
			tutor_load_template('single.quiz.previous-attempts', compact('previous_attempts', 'quiz_id'));
			$previous_attempts_html = ob_get_clean();
            echo apply_filters('tutor_quiz/previous_attempts_html', $previous_attempts_html, $previous_attempts, $quiz_id);

			do_action('tutor_quiz/previous_attempts/after', $previous_attempts, $quiz_id);
		} ?>
	<?php
		if ($attempt_remaining > 0 || $attempts_allowed == 0 && $previous_attempts) {
		do_action('tuotr_quiz/start_form/before', $quiz_id);
	?>
	<div class="tutor-quiz-btn-grp tutor-mt-30">
		<form id="tutor-start-quiz" method="post">
			<?php wp_nonce_field( tutor()->nonce_action, tutor()->nonce ); ?>

			<input type="hidden" value="<?php echo $quiz_id; ?>" name="quiz_id"/>
			<input type="hidden" value="tutor_start_quiz" name="tutor_action"/>

			<button type="submit" class="tutor-btn tutor-btn-primary tutor-btn-md start-quiz-btn" name="start_quiz_btn" value="start_quiz">
				<?php _e( 'Start Quiz', 'tutor' ); ?>
			</button>
		</form>
	</div>
	<?php } ?>
	<?php
	}


	//tutor_next_previous_pagination();

    do_action('tutor_quiz/body/after', $quiz_id);
	?>
</div>

<script>
	window.tutor_quiz_context = '<?php echo strrev(base64_encode(json_encode($quiz_answers))); ?>';
</script>