<?php

namespace ultlcNamEelementorPlugiN;

use Elementor\Controls_Manager;
use Elementor\Element_Base;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class ClassULTLCElementorP {
	static $ultlc_should_script_enqueue = false;

    private static $_instance = null;

    public static function instance() {
        
		add_action('wp_footer', [__CLASS__, 'ultlc_enqueue_scripts']);

		add_action('wp_ajax_get_section_data', [__CLASS__, 'ultlc_get_section_data']);
		add_action('wp_ajax_nopriv_get_section_data', [__CLASS__, 'ultlc_get_section_data']);

		add_action('elementor/frontend/section/before_render', [__CLASS__, 'ultlc_should_script_enqueue']);
		add_action('elementor/frontend/container/before_render', [__CLASS__, 'ultlc_should_script_enqueue']);

		add_action('elementor/element/section/_section_ultlc_live_copy/after_section_start', [__CLASS__, 'ultlc_register_controls']);
		add_action('elementor/element/container/_section_ultlc_live_copy/after_section_start', [__CLASS__, 'ultlc_register_controls']);

        // For register section
		// Activate sections for widgets
		add_action( 'elementor/element/common/_section_style/after_section_end', [ __CLASS__, 'ultlc_add_controls_sections' ], 1, 2 );
		// Activate column for sections
		add_action( 'elementor/element/column/section_advanced/after_section_end', [ __CLASS__, 'ultlc_add_controls_sections' ], 1, 2 );
		// Activate sections for sections
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ __CLASS__, 'ultlc_add_controls_sections' ], 1, 2 );

		add_action( 'elementor/element/container/section_layout/after_section_end', [ __CLASS__, 'ultlc_add_controls_sections' ], 1, 2 );

        add_action( 'elementor/editor/after_enqueue_scripts', [ __CLASS__, 'ultlc_enqueue' ] );

        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function ultlc_enqueue() {
        $src = plugin_dir_url(__FILE__) . 'assets/public/js/marvin-new.min.js';
        $dependencies = [ 'elementor-editor' ];

        wp_enqueue_script(
            'marvin-fff',
            $src,
            $dependencies,
            '1.0',
            true
        );

        wp_localize_script(
            'marvin-fff',
            'marvin',
            [
                'storagekey' => md5( 'LICENSE KEY' ),
                'ajax_url'    => admin_url( 'admin-ajax.php' ),
                'nonce'      => wp_create_nonce('ultlc_get_section_data'),
            ]
        );
	}

    public static function ultlc_add_controls_sections( $element, $args ) {
		$element->start_controls_section(
			'_section_ultlc_live_copy',
			[
				'label' => __( 'BWD Live Copy', 'ultimate-live-copy' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->end_controls_section();
	}

    public static function ultlc_should_script_enqueue(Element_Base $section) {
		if (self::$ultlc_should_script_enqueue) {
			return;
		}

		if ('yes' == $section->get_settings_for_display('_ultlc_enable_live_copy')) {
			self::$ultlc_should_script_enqueue = true;
			remove_action('elementor/frontend/section/before_render', [__CLASS__, 'ultlc_should_script_enqueue']);
			remove_action('elementor/frontend/container/before_render', [__CLASS__, 'ultlc_should_script_enqueue']);
		}
	}

    public function get_name() {
        return 'ultlc-threed-text';
    }

    public static function ultlc_register_controls(Element_Base $section) {

		$section->add_control(
			'_ultlc_enable_live_copy',
			[
				'label' => __('BWD Enable Live Copy', 'ultimate-live-copy'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'frontend_available' => true,
				'render_type' => 'none'
			]
		);
        
        $section->add_control(
			'ultlc_live_copy_display',
			[
				'label' => esc_html__( 'Hover?', 'ultimate-live-copy' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'none' => [
						'title' => esc_html__( 'Hover', 'ultimate-live-copy' ),
						'icon' => 'eicon-check-circle-o',
					],
					'block' => [
						'title' => esc_html__( 'Fixed', 'ultimate-live-copy' ),
						'icon' => 'eicon-ban',
					],
				],
				'toggle' => true,
				'selectors' => [
					'.e-con > .ultlc-live-copy-wrap' => 'display: {{VALUE}};',
				],
			]
		);
        $section->add_control(
			'ultlc_live_copy_left',
			[
				'label' => esc_html__( 'Left', 'ultimate-live-copy' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
                ],
				'selectors' => [
					'.e-con > .ultlc-live-copy-wrap' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);
        $section->add_control(
			'ultlc_live_copy_right',
			[
				'label' => esc_html__( 'Right', 'ultimate-live-copy' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
                ],
				'selectors' => [
					'.e-con > .ultlc-live-copy-wrap' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$section->add_responsive_control(
			'_ultlc_live_copy_btn_padding',
			[
				'label' => __('Padding', 'ultimate-live-copy'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'separator' => 'before',
				'selectors' => [
					'.elementor .ultlc-live-copy-wrap .ultlc-live-copy-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$section->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => '_ultlc_live_copy_btn_border',
				'selector' => '.elementor .ultlc-live-copy-wrap .ultlc-live-copy-btn',
			]
		);

		$section->add_control(
			'_ultlc_live_copy_btn_border_radius',
			[
				'label' => __('Border Radius', 'ultimate-live-copy'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'.elementor .ultlc-live-copy-wrap .ultlc-live-copy-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$section->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => '_ultlc_live_copy_btn_box_shadow',
				'selector' => '.elementor .ultlc-live-copy-wrap .ultlc-live-copy-btn',
			]
		);

		$section->start_controls_tabs('_ultlc_live_copy_btn_tabs');

		$section->start_controls_tab(
			'_ultlc_live_copy_btn_tab_normal',
			[
				'label' => __('Normal', 'ultimate-live-copy'),
			]
		);

		$section->add_control(
			'_ultlc_live_copy_btn_color',
			[
				'label' => __('Text Color', 'ultimate-live-copy'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.elementor .ultlc-live-copy-wrap .ultlc-live-copy-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$section->add_control(
			'_ultlc_live_copy_btn_bg_color',
			[
				'label' => __('Background Color', 'ultimate-live-copy'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.elementor .ultlc-live-copy-wrap .ultlc-live-copy-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$section->end_controls_tab();

		$section->start_controls_tab(
			'_ultlc_live_copy_btn_tab_hover',
			[
				'label' => __('Hover', 'ultimate-live-copy'),
			]
		);

		$section->add_control(
			'_ultlc_live_copy_btn_hover_color',
			[
				'label' => __('Text Color', 'ultimate-live-copy'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.elementor .ultlc-live-copy-wrap .ultlc-live-copy-btn:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$section->add_control(
			'_ultlc_live_copy_btn_hover_bg_color',
			[
				'label' => __('Background Color', 'ultimate-live-copy'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.elementor .ultlc-live-copy-wrap .ultlc-live-copy-btn:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$section->add_control(
			'_ultlc_live_copy_btn_hover_border_color',
			[
				'label' => __('Border Color', 'ultimate-live-copy'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'_ultlc_live_copy_btn_border_border!' => '',
				],
				'selectors' => [
					'.elementor .ultlc-live-copy-wrap .ultlc-live-copy-btn:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$section->end_controls_tab();
		$section->end_controls_tabs();

		// $section->end_controls_section();
    }

    // ============ Start live copy =============//
	protected static function add_button() {
        echo '<div id="ultlc-live-copy-base" class="ultlc-live-copy-wrap" style="display: none">';
            echo '<a class="ultlc-live-copy-btn" href="#" class="" target="_blank">'.esc_html('Live Copy', 'ultimate-live-copy').'</a>';
        echo '</div>';
    }

    protected static function add_inline_style() {
        echo "<style>
            .elementor-section-wrap > .elementor-section,
            .elementor-section.elementor-top-section,
            .e-container,
            .e-con {
                position: relative;
            }
            .ultlc-live-copy-wrap,
            .elementor-section-wrap .ultlc-live-copy-wrap,
            .elementor-section.elementor-top-section .ultlc-live-copy-wrap,
            .e-container > .ultlc-live-copy-wrap,
            .e-con > .ultlc-live-copy-wrap {
                position: absolute;
                top: 50%;
                z-index: 99999;
                text-decoration: none;
                font-size: 15px;
                transform: translateY(-50%);
                border-radius: 4px;
            }
            .ultlc-live-copy-wrap .ultlc-live-copy-btn {
				display: block;
                padding: 8px 12px;
                border-radius: 4px;
                background: #007bff;
                color: #fff;
                line-height: 1;
                transition: background-color 0.2s, transform 0.2s;
            }
            .ultlc-live-copy-wrap .ultlc-live-copy-btn:focus {
                padding: 8px 12px;
                border-radius: 4px;
                background: #016de0;
                color: #fff;
                line-height: 1;
                transition: background-color 0.2s, transform 0.2s;
            }
            .ultlc-live-copy-wrap .ultlc-live-copy-btn:hover {
                background: #0056b3;
                transform: scale(1.05);
                box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
                cursor: pointer;
            }
			.elementor-section-wrap>.elementor-section.live-copy-preview .ultlc-live-copy-wrap,
			.elementor-section.elementor-top-section.live-copy-preview .ultlc-live-copy-wrap,
			.elementor-section-wrap>.elementor-section:not(.elementor-element-edit-mode):hover .ultlc-live-copy-wrap,
			.elementor-section.elementor-top-section:not(.elementor-element-edit-mode):hover .ultlc-live-copy-wrap,
			.e-container:not(.elementor-element-edit-mode):hover .ultlc-live-copy-wrap,
			.e-con:not(.elementor-element-edit-mode):hover .ultlc-live-copy-wrap {
				display: block
			}
        </style>";
    }

    public static function ultlc_enqueue_scripts() {
        if (ultlc_elementor()->preview->is_preview_mode()) {
            self::add_inline_style();
            self::add_button();
            return;
        }
    
        if (self::$ultlc_should_script_enqueue) {
            self::add_inline_style();
            self::add_button();
    
            wp_enqueue_script('live-copy-fff', plugin_dir_url(__FILE__) . 'assets/public/js/live-copy.min.js', ['jquery'], '1.0', true);
    
            // Localize script with the correct handle
            wp_localize_script(
                'live-copy-fff', // Correct handle of the enqueued script
                'livecopy',
                [
                    'storagekey' => md5('LICENSE KEY'),
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('ultlc_get_section_data'),
                ]
            );
        }
    }
    
    public static function ultlc_get_section_data() {
        /**
         * This check doesn't need any conditional block
         * when 3rd parameter (die) is true.
         */
        check_ajax_referer('ultlc_get_section_data', 'nonce');

        $post_id = isset($_GET['post_id']) ? absint($_GET['post_id']) : 0;
        $section_id = isset($_GET['section_id']) ? sanitize_text_field($_GET['section_id']) : 0;
        $elType = isset($_GET['elType']) ? sanitize_text_field($_GET['elType']) : '';

        if (empty($post_id) || empty($section_id)) {
            wp_send_json_error('Incomplete request');
        }

        $is_built_with_elementor = ultlc_elementor()->documents->get( $post_id )->is_built_with_elementor();

        if (!$is_built_with_elementor) {
            wp_send_json_error('Not built with elementor');
        }

        $document = ultlc_elementor()->documents->get($post_id);
        $elementor_data = $document ? $document->get_elements_data() : [];
        $data = [];

        if (!empty($elementor_data)) {
            $data = wp_list_filter($elementor_data, [
                'id' => $section_id,
                'elType' => $elType,
                // 'elType' => 'section',
            ]);

            $data = current($data);

            if (empty($data)) {
                wp_send_json_error('Section not found');
            }
        }

        wp_send_json_success($data);
    }
    // ============ End live copy =============//

    public function ultlc_admin_live_copy_enqueue_scripts(){
        wp_enqueue_style('icon-live-copy-style-min-loaded', plugin_dir_url(__FILE__).'assets/admin/css/icon.css',null,'1.0','all');
    }

    public function __construct() {
        add_action('elementor/editor/before_enqueue_scripts', [$this, 'ultlc_admin_live_copy_enqueue_scripts']);
    }
}

// Instantiate Plugin Class
ClassULTLCElementorP::instance();
