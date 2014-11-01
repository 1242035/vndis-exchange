<?php

/**
 * Plugin Name: Vndis Exchange
 * Plugin URI: http://vndis.com
 * Description: Display Exchange
 * Version: 1.0
 * Author: Vndis
 * Author URI: http://vndis.com
 * License: GPL2
 */
//if(!defined(ABSPATH)) die('No permission to access.');
class Vndis_Exchange extends WP_Widget
{

	/**
	 * Sets up the widgets name etc
	 */
	private $text_domain;
	private $base_path;
	private $base_url;
	private $exchange;
	public function __construct()
	{
		$this->text_domain = 'vndis-exchange';
		$this->base_path = dirname(__FILE__);
		$this->base_url  = plugins_url('', __FILE__);
		$this->exchange = 'http://www.vietcombank.com.vn/ExchangeRates/ExrateXML.aspx';
		add_action('init', array($this, 'load_style') );
		//add_action('init', array($this, 'load_script') );

		parent::__construct(
			'vndis_exchange', // Base ID
			__('Vndis Exchange', $this->text_domain), // Name
			array( 'description' => __( 'Add Exchange to widget', $this->text_domain ), ) // Args
		);

	}

	public function load_style()
	{
		//wp_register_style('vndis-github-css', $this->base_url. '/static/css/vndis-github.css');
	}
	public function load_script()
	{
		wp_register_script('vndis-github-js', $this->base_url. '/static/js/vndis-github.js');
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance )
	{
		//wp_enqueue_style('vndis-github-css');
		//wp_enqueue_script('vndis-github-js');
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		?>
		<div>
            <?php $this->getRss(); ?>
		</div>
		<?php
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 *
	 * @return string|void
	 */
	private function getRss()
	{
		$xml = new DOMDocument();
		$xml->load($this->exchange);
		echo '<table>';
		echo '<tr>
				<th>Code</th>
				<th>Name</th>
				<th>Buy</th>
				<th>Tranfer</th>
				<th>Sell</th>
			  </tr>';
		foreach($xml->getElementsByTagName('Exrate') as $link) {
			printf(
				'<tr><td>%s</td><td>%s</td><td>%.2f</td><td>%.2f</td><td>%.2f</td></tr>',
				esc_attr( $link->getAttribute('CurrencyCode') ),
				esc_attr( $link->getAttribute('CurrencyName') ),
				esc_attr( $link->getAttribute('Buy') ),
				esc_attr( $link->getAttribute('Transfer') ),
				esc_attr( $link->getAttribute('Sell') )
			);
		}
		$time = $xml->getElementsByTagName('DateTime')->item(0);
		printf('<tr><td colspan="2">Update Time: </td><td colspan="3">%s</td></tr>',esc_attr($time->nodeValue));
		echo '</table>';

	}
	public function form( $instance )
	{
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else
		{
			$title = __( 'Title', $this->text_domain );
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' , $this->text_domain); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
	<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance )
	{
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}
}

add_action( 'widgets_init', function ()
{
	register_widget( 'Vndis_Exchange' );
} );