<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* @author Muhamad Ridwan
* @since 2015
*/

class Template
{
	protected 	$ci;
	var $partial 	= array();
	var $template 	= 'front/front_default_layout';
	var $data = array();


	public function __construct()
	{
		$this->ci =& get_instance();
		$this->data['partial'] = $this->partial;
	}

	public function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if(isset($this->$key))
			{
				$this->$key = $val;
			}
		}
	}

	public function set_partial($partial = array(), $view = NULL, $data = array())
	{
		$this->set_data($this->partial);
		if(is_array($partial))
		{
			foreach($partial as $key => $val)
			{
				$this->partial['partial'][$key] = $this->render_partial($val, $this->partial, TRUE);
			}
		}
		else
		{
			$this->partial['partial'][$partial] = $this->render_partial($view, $this->data, TRUE);
		}
	}

	public function set_template($template_view = NULL)
	{
		$this->template = $template_view;
	}

	public function set_data($data = array())
	{
		foreach ($data as $key => $val)
		{
			$this->data[$key] = $val;
		}
	}

	public function render($view = NULL, $data = array())
	{
		//set partial data on render
		

		$this->set_data($data);
		$this->set_data($this->partial);
		$this->data['partial']['content'] = $this->render_partial($view, $this->data);

		
		$this->ci->load->view($this->template, $this->data);
	}

	public function render_partial($view = NULL, $data = array())
	{
		$this->set_data($data);
		return $this->ci->load->view($view, $this->data, TRUE);
	}

}

/* End of file Template.php */
/* Location: ./application/libraries/Template.php */
