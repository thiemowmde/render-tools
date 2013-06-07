<?php
class Asqm_JsonView extends View {
	
	public function __construct( $templateName ) {
		parent::__construct( $templateName );
	}

	public function getJson() {
		# get callback id
		$callback = SingletonFactory::getInstance('Request')->getVar('callback');
		
		# collect results
		$model = SingletonFactory::getInstance( "Asqm_Json" );
		$model->setView( $this );
		$result = $model->getResults();

		return json_encode( $result );
	}
}
