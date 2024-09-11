<?php
namespace TwwcProtein\Tests;

use TwwcProtein\Admin\TwwcAdminMenu;
use WP_UnitTestCase;

class Test_TwwcAdminMenu extends WP_UnitTestCase {
    private $float_keys = [
        'one_key' => '',
        'two_key' => '7.5'
    ];

    private $protein_settings_input = [];

    /**
     * @covers TwwcProtein\Admin\TwwcAdminMenu::generate_value_string
     * @group adminMenu
     */
    public function test_generate_value_string_returns_string() {
        $admin_menu = new TwwcAdminMenu();
        $this->assertEquals('test', $admin_menu->generate_value_string('test',''));
    }

    /**
     * @covers TwwcProtein\Admin\TwwcAdminMenu::generate_value_float
     * @group adminMenu
     */
    public function test_generate_value_float_returns_defaults_with_missing_keys() {
        $valid_input = [];
        $input = [
            'wrongkey' => '',
            'wrongkeyagain' => ''
        ];

        $twwcAdminMenu = new TwwcAdminMenu();

        foreach($this->float_keys as $key => $default) {
            $valid_input[$key] = isset($input[$key]) ? $twwcAdminMenu->generate_value_float($input[$key]) : $default;
        }

        $this->assertEquals($this->float_keys, $valid_input);
    }

    /**
     * @covers TwwcProtein\Admin\TwwcAdminMenu::generate_value_float
     * @group adminMenu
     */
    public function test_generate_value_float_returns_null_with_zero() {
        $valid_input = [];

        $input = [
            'one_key' => 0,
            'two_key' => 0
        ];

        $twwcAdminMenu = new TwwcAdminMenu();

        foreach($this->float_keys as $key => $default) {
            $valid_input[$key] = isset($input[$key]) ? $twwcAdminMenu->generate_value_float($input[$key]) : $default;
        }

        $this->assertEquals([
            'one_key' => null,
            'two_key' => null
        ], $valid_input);
    }

    public function test_generate_value_float_returns_valid_float() {
        $valid_input = [];

        $input = [
            'one_key' => .61,
            'two_key' => 1.34
        ];

        $twwcAdminMenu = new TwwcAdminMenu();

        foreach($this->float_keys as $key => $default) {
            $valid_input[$key] = isset($input[$key]) ? $twwcAdminMenu->generate_value_float($input[$key]) : $default;
        }

        $this->assertEquals([
           'one_key' => .61,
            'two_key' => 1.34 
        ], $valid_input);
    }

    /**
     * @covers TwwcProtein\Admin\TwwcAdminMenu::generate_value_array
     * @group adminMenu
     */
    public function test_generate_array_int_returns_array() {
        $array = [
            'one_key' => 0,
            'two_key' => 0
        ];

        $twwcAdminMenu = new TwwcAdminMenu();

        $this->assertEquals($array, $twwcAdminMenu->generate_value_array($array));
    }

    /**
     *@covers TwwcProtein\Admin\TwwcAdminMenu::convert_multiplier_to_lbs_value
     * @group adminMenu
     */
    public function test_convert_multiplier_to_lbs_value() {
        $multiplier_weight_kg = 1.2;

        $twwcAdminMenu = new TwwcAdminMenu();

        $this->assertEquals('.54', $twwcAdminMenu->convert_multiplier_to_lbs_value($multiplier_weight_kg));
    }

    /**
     * @covers TwwcProtein\Admin\TwwcAdminMenu::convert_multiplier_to_lbs_value
     * @group adminMenu
     */
    public function test_convert_multiplier_to_kg_value() {
        $multiplier_weight_lbs = .54;

        $twwcAdminMenu = new TwwcAdminMenu();

        $this->assertEquals('1.19', $twwcAdminMenu->convert_multiplier_to_kg_value($multiplier_weight_lbs));
    }

    /**
     * @covers TwwcProtein\Admin\TwwcAdminMenu::generate_valid_goal_values
     * @group adminMenu
     */
    public function test_generate_goal_values_returns_valid_array_lbs() {
        $goals = [
            'm_maintain_lbs' => '',
            'm_maintain_kg' => '',
            'm_maintain_high_lbs' => '',
            'm_maintain_high_kg' => '',
        ];

        $protein_settings_input['system'] = 'imperial';

        $protein_settings_input['activity_level']['sedentary']['goal'] = [
            'm_maintain_lbs' => '0.68',
            'm_maintain_kg' => '',
            'm_maintain_high_lbs' => '0.95',
            'm_maintain_high_kg' => ''
        ];

        $twwcAdminMenu = $this->getMockBuilder(TwwcAdminMenu::class)
            ->onlyMethods(['get_protein_settings_input'])
            ->getMock();

        $twwcAdminMenu->set_protein_settings_input($protein_settings_input);

        $this->assertEquals([
            'm_maintain_lbs' => '0.68',
            'm_maintain_kg' => '1.5',
            'm_maintain_high_lbs' => '0.95',
            'm_maintain_high_kg' => '2.09'
        ], $twwcAdminMenu->generate_valid_goal_values($goals, 'sedentary'));
    }

    /**
     * @covers TwwcProtein\Admin\TwwcAdminMenu::generate_valid_goal_values
     * @group adminMenu
     */
    public function test_generate_goal_values_returns_valid_array_kg() {
        $goals = [
            'm_maintain_lbs' => '',
            'm_maintain_kg' => '',
            'm_maintain_high_lbs' => '',
            'm_maintain_high_kg' => '',
        ];

        $protein_settings_input['system'] = 'metric';

        $protein_settings_input['activity_level']['sedentary']['goal'] = [
            'm_maintain_lbs' => '',
            'm_maintain_kg' => '1.2',
            'm_maintain_high_lbs' => '',
            'm_maintain_high_kg' => '1.8'
        ];

        $twwcAdminMenu = $this->getMockBuilder(TwwcAdminMenu::class)
            ->onlyMethods(['get_protein_settings_input'])
            ->getMock();

        $twwcAdminMenu->set_protein_settings_input($protein_settings_input);

        $this->assertEquals([
            'm_maintain_lbs' => '0.54',
            'm_maintain_kg' => '1.2',
            'm_maintain_high_lbs' => '0.82',
            'm_maintain_high_kg' => '1.8'
        ], $twwcAdminMenu->generate_valid_goal_values($goals, 'sedentary'));
    }
}