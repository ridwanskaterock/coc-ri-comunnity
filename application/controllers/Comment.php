<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comment extends front
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_comment');
	}

	public function base_comment()
	{
		$this->form_validation->set_rules('comment', 'Comment', 'required');

		if ($this->form_validation->run()) {
			$comment = $this->input->post('comment');
			$idbase = $this->input->post('idbase');
			$rating = $this->input->post('rating');

			$data = array(
				'comment_table_reff' => 'base',
				'comment_table_reff_id' => $idbase,
				'comment_text' => htmlspecialchars($comment),
				'comment_created_date' => now(),
				'comment_created_by' => user_member('iduser'),
				'comment_rating_count' => (int) $rating,
			);

			$idcomment = $this->model_comment->store($data);

			if ($idcomment) {
				$data_comment = array(
					'row' => $this->model_comment->find_comment_by_idcomment($idcomment)
					);

				$data_html = $this->load->view('front/comment/partial/comment-base', $data_comment, TRUE);

				$outs = array(
					'flag' => 1, 
					'data' => $data,
					'html' => $data_html,
					'msg' => 'Success post your comment'
				);

				echo json_encode($outs);
			} else {
				$outs = array(
					'flag' => 0, 
					'msg' => 'Error post comment, Please try again.'
				);

				echo json_encode($outs);
			}

		} else {
			$outs = array(
				'flag' => 0, 
				'msg' => validation_errors()
			);

			echo json_encode($outs);
		}
	}

}

/* End of file Comment.php */
/* Location: ./application/controllers/Comment.php */