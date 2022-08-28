jQuery(function($) {

	/**
	 * Toggles loading indicator in score widget
	 * @param loading
	 */
	function setLoading(loading) {
		if (loading) {
			$(".cream-fab-counter p").hide();
			$(".cream-fab-counter i").show();
		} else {
			$(".cream-fab-counter p").show();
			$(".cream-fab-counter i").hide();
		}
	}

	/**
	 * Makes a AJAX request to the server
	 * @param endPoint WordPress ajax endpoint
	 * @param doneFn Result callback
	 * @param data data to POST
	 */
	function request(endPoint, doneFn, data) {

		setLoading(true);

		$.post(def.ajaxurl + '?action=' + endPoint, data)
			.done(doneFn)
			.fail(function (e) {
				console.log(e);
			})
			.always(function () {
				setLoading(false);
			});
	}

	/**
	 * Updates a users score for the current post
	 * @param obj
	 */
	function updateScore(obj) {
		request('update_score', function(res) {
			var datum = JSON.parse(res);
			$(".cream-fab-counter p").html(datum.total_score);
		}, obj);
	}

	/**
	 * Click event for the "up" arrow, changes users score to +1
	 */
	$("#cream-icon-up").click(function (e) {
		$(e.target).addClass('cream-icon-selected');
		$("#cream-icon-down").removeClass('cream-icon-selected');

		updateScore({
			score: 1,
			post_id: def.post_id
		});
	});

	/**
	 * Click event for the "down" arrow, changes users score to -1
	 */
	$("#cream-icon-down").click(function (e) {
		$(e.target).addClass('cream-icon-selected');
		$("#cream-icon-up").removeClass('cream-icon-selected');

		updateScore({
			score: -1,
			post_id: def.post_id
		});
	});

	/**
	 * Fetches initial state of score widget
	 */
	$(document).ready(function () {
		request('fetch_scores', function(res) {
			var datum = JSON.parse(res);
			console.log(datum);
			$(".cream-fab-counter p").html(datum.total_score);
			if (datum.user_score === -1) {
				$("#cream-icon-down").addClass('cream-icon-selected');
			} else if (datum.user_score === 1) {
				$("#cream-icon-up").addClass('cream-icon-selected');
			}
		}, {post_id: def.post_id});
	})
});