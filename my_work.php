<?php


	$filename_1 = "contracts.csv";	//Given data file "contracts.csv"
	$filename_2 = "awards.csv";	//Given data file "awards.csv"
	$output_file = "final.csv";	//The output file for the commom contractNames.
	$total_amount = 0;		// The required output for the closed awarded contracts.

/* To get the data of csv format in the array format from each individual given csv files
*/
	$data_contracts = extract_data($filename_1);
	$data_awarded = extract_data($filename_2);

/* To extract the column heads from the respective given csv file 
	 THE REMAINING DATA IN THE variables "$data_contracts" and "$data_awards" are only the actual data, excluding the header-columns
	$head_column_1 and $head_column_2 contains the header columns for the respective csv files contracts.csv and awards.csv
*/

	$head_column_1 = array_shift($data_contracts);
	$head_column_2 = array_shift($data_awarded);



/* Output the data into the file "final.csv" ...."final.csv" is created dynamically after the execution of this file.

*/

	$file_pointer = fopen($output_file,"w");	
		$output_data = array();	//This array that will be used to output the actual csv format.
		$temp_array = array(); 	//Temporary array for manupulations.
		if(!$file_pointer)
			echo "File unable to create";


		$header= array("contractname");			//Problem statement has mentioned the case-sensitive "N" in contract"N"ame
		output_header($head_column_1,&$header);
		output_header($head_column_2,&$header);
		array_push($output_data,$header);	// The required headers are establised for "final.csv" file.

		foreach($data_contracts as $x){
			$equals = 0;
			foreach($data_awarded as $y){
				$equals = check_contract_name($x,$y);
				if($equals == 1){ 	//checks if the Contract-names are equals, if true merge the two arrays contracts and awarded.
						
					$closed_or_not = check_closed($x);
					if($closed_or_not == 1)
						$total_amount = $total_amount + $y[5];		//TO get the sum of closed awarded contracts.
				

					array_shift($y);	//TO remove the common contract-name
					$temp_array = $x;
						foreach($y as $value){			// $value is appended to the $data_contracts array
							array_push($temp_array,$value);
						}
					array_push($output_data,$temp_array); 	// FOr the actual output in csv format.
					break;
	
				}
			}
				if($equals==0){
					$empty_array= array("" ,"" ,"" ,"" ,"" );	// empty array for non matching contracts
					$temp_array = $x ;
						foreach($empty_array as $value){	// $value is appended to the $data_contracts array
							array_push($temp_array,$value);
						}
					array_push($output_data,$temp_array);
				}
		
			
		}
		

		foreach($output_data as $row){		//Each row is manipulated to convert to csv format for "final.csv"
		fputcsv($file_pointer,$row);
		}
		fclose($file_pointer);
			
		echo "Total Amount of closed contracts: {$total_amount}";


	
/*FUNCTIONS and body
	BOOLEAN TRUE = 1 and BOOLEAN FALSE = 0 used.
*/
	function extract_data($filename){
		/*Function to extract the data of each given file in csv format to the simple array format for calculations and manipulations.
		*/
		$data = array();		//$data is temporary array which is returned.
		$fp = fopen("$filename","r");
			while(!feof($fp)){
				$value = fgetcsv($fp);
				array_push($data,$value);
			}
		return $data;
		fclose($fp);
	}
		
	function output_header($y,&$x){
		/* The each header from the file "contracts.csv" and "awarded.csv" is extracted except the common "contractname"
		   Since the problem statement has mentioned the common "contractname", the case sensitivity of "N" differs in both the given file.
		   Case sensitivity is managed.
		*/
		foreach($y as $value){
			if( $value != "contractname" && $value != "contractName")	// contract"n"ame and contract"N"ame are different so check to 													either is made
			array_push($x,$value);
		}
	}
		
	function check_contract_name($x,$y){
		if($x[0] == $y[0] )				
			return 1;		// boolean TURU, contractname are same
		else 
			return 0;		// boolean FALSE, contractnames are different
	}

	function check_closed($value){
		if( $value[1] == "Closed" )
			return 1;	// boolean true, Contract is closed
		else 
			return 0;	// boolean false, Contract is current
	}

