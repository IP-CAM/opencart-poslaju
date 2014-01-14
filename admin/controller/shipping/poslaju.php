<?php
class ControllerShippingPoslaju extends Controller { 
	private $error = array();
	
	public function index() {  
		# Load the language
		$this->language->load('shipping/poslaju');

		# Set the heading title
		$this->document->setTitle($this->language->get('heading_title'));
		
		# Load the settings
		$this->load->model('setting/setting');
				 
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('poslaju', $this->request->post);	

			$this->session->data['success'] = $this->language->get('text_success');
									
			$this->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		# Set context to send to template
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		
		$this->data['entry_rate'] = $this->language->get('entry_rate');
		$this->data['entry_tax_class'] = $this->language->get('entry_tax_class');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');

		# Set flash message on error
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		# Build breadcrumbs
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_shipping'),
			'href'      => $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('shipping/poslaju', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		# Set url for submit form
		$this->data['action'] = $this->url->link('shipping/poslaju', 'token=' . $this->session->data['token'], 'SSL');
		
		# Set link for cancel button
		$this->data['cancel'] = $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'); 

		# Load localisation - geo zones
		$this->load->model('localisation/geo_zone');
		
		# Get current localisation geo zones
		$geo_zones = $this->model_localisation_geo_zone->getGeoZones();
		
		foreach ($geo_zones as $geo_zone) {
			# Check if there is POST data for this particular field (rate)
			if (isset($this->request->post['poslaju_' . $geo_zone['geo_zone_id'] . '_rate'])) {
				# If exists, put into local data array $geo_zone
				$this->data['poslaju_' . $geo_zone['geo_zone_id'] . '_rate'] = $this->request->post['poslaju_' . $geo_zone['geo_zone_id'] . '_rate'];
			} else {
				# If not exist, get from settings db (config)
				$this->data['poslaju_' . $geo_zone['geo_zone_id'] . '_rate'] = $this->config->get('poslaju_' . $geo_zone['geo_zone_id'] . '_rate');
			}		
			
			# Check if there is POST data for this particular field (status)
			if (isset($this->request->post['poslaju_' . $geo_zone['geo_zone_id'] . '_status'])) {
				# If exists, put into local data array $geo_zone
				$this->data['poslaju_' . $geo_zone['geo_zone_id'] . '_status'] = $this->request->post['poslaju_' . $geo_zone['geo_zone_id'] . '_status'];
			} else {
				# If not exist, get from settings db (config)
				$this->data['poslaju_' . $geo_zone['geo_zone_id'] . '_status'] = $this->config->get('poslaju_' . $geo_zone['geo_zone_id'] . '_status');
			}		
		}
		
		# Assign compiled array to $data[] array
		$this->data['geo_zones'] = $geo_zones;

		# Check if there is POST data for this particular field
		if (isset($this->request->post['poslaju_tax_class_id'])) {
			# If exists, put into $data[] array
			$this->data['poslaju_tax_class_id'] = $this->request->post['poslaju_tax_class_id'];
		} else {
			# If not exist, get from settings db (config)
			$this->data['poslaju_tax_class_id'] = $this->config->get('poslaju_tax_class_id');
		}
		
		# Follow the same concept as above, for tax
		$this->load->model('localisation/tax_class');
				
		# Fetch tax classes from db
		$this->data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		
		# Check for module status is POST
		if (isset($this->request->post['poslaju_status'])) {
			# If exists, set to local $data[] array
			$this->data['poslaju_status'] = $this->request->post['poslaju_status'];
		} else {
			# Else get from database
			$this->data['poslaju_status'] = $this->config->get('poslaju_status');
		}
		
		# Same as above
		if (isset($this->request->post['poslaju_sort_order'])) {
			$this->data['poslaju_sort_order'] = $this->request->post['poslaju_sort_order'];
		} else {
			$this->data['poslaju_sort_order'] = $this->config->get('poslaju_sort_order');
		}	

		# Set target template to send context to
		$this->template = 'shipping/poslaju.tpl';

		# Set common header and footer
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		# Render response
		$this->response->setOutput($this->render());
	}
		
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'shipping/poslaju')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>
