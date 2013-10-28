<?php

/*
=====================================================
 Get visitor's geolocation data using IPGeoBase.ru
-----------------------------------------------------
 http://www.intoeetive.com/
-----------------------------------------------------
 Copyright (c) 2013 Yuri Salimovskiy
=====================================================
 This software is intended for usage with
 ExpressionEngine CMS, version 2.0 or higher
=====================================================
 File: pi.ipgeobase.php
-----------------------------------------------------
 Purpose: Get visitor's geolocation data using IPGeoBase.ru
=====================================================
*/


$plugin_info = array(
		'pi_name'			=> 'Ipgeobase',
		'pi_version'		=> '0.1',
		'pi_author'			=> 'Yuri Salimovskiy',
		'pi_author_url'		=> 'http://www.intoeetive.com/',
		'pi_description'	=> "Get visitor's geolocation data using IPGeoBase.ru",
		'pi_usage'			=> Ipgeobase::usage()
	);


class Ipgeobase {

    var $return_data;
    
    /** ----------------------------------------
    /**  Constructor
    /** ----------------------------------------*/

    function __construct()
    {        
    	$this->EE =& get_instance(); 
    }
    /* END */	    

    
    /** ----------------------------------------
    /**  Count
    /** ----------------------------------------*/

    function location()
    {
		$data = array (
		  "range"=>"",
		  "cc"=>"",
		  "city"=>"",
		  "region"=>"",
		  "district"=>"",
		  "lat"=>"",
		  "lng"=>""
		);
		
		if ($this->EE->session->userdata('member_id')!=0 && $this->EE->session->userdata('location')!='')
		{
			$data['region'] = $this->EE->session->userdata('location');
		}
		else
		{
			require_once(PATH_THIRD."ipgeobase/ipgeobase.php");
			$gb = new IPGeoBaseScript();
			$data = $gb->getRecord($this->EE->input->ip_address);
			if ($this->EE->session->userdata('member_id')!=0)
			{
				$upd = array('location'=>$data['region']);
				$this->EE->db->where('member_id', $this->EE->session->userdata('member_id'));
				$this->EE->db->update('members', $upd);
			}
		}

		$this->return_data = $this->EE->TMPL->parse_variables_row(trim($this->EE->TMPL->tagdata), $data);
       	
       	return $this->return_data;

    }
    /* END */
    
// ----------------------------------------
//  Plugin Usage
// ----------------------------------------

// This function describes how the plugin is used.
//  Make sure and use output buffering

function usage()
{
ob_start(); 
?>
Get visitor's geolocation data using IPGeoBase.ru:
{exp:ipgeobase:location}
{range}
{cc} ::
{city} ::
{region} ::
{district} ::
{lat} ::
{lng}
{/exp:ipgeobase:location}

<?php
$buffer = ob_get_contents();
	
ob_end_clean(); 

return $buffer;
}
/* END */


}
// END CLASS
?>