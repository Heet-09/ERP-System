<?php
namespace Phppot;

use Phppot\DataSource;
class UserModel
{

    private $conn;

    function __construct()
    {
        require_once 'DataSource.php';
        $this->conn = new DataSource();
    }
	 function readUserRecords()
    {
        $fileName = $_FILES["file"]["tmp_name"];
        if ($_FILES["file"]["size"] > 0) {
            $file = fopen($fileName, "r");
            $importCount = 0;
            while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
				//echo "inside while";
                if (! empty($column) && is_array($column)) {
                    if ($this->hasEmptyRow($column)) {
                        continue;
                    }
                    //if (isset($column[1], $column[2], $column[3], $column[4], $column[5], $column[6], $column[7], $column[8], $column[9], $column[10], $column[11], $column[12], $column[13], $column[14], $column[15], $column[16], $column[17], $column[18], $column[19], $column[20], $column[21], $column[22], $column[23], $column[24], $column[25], $column[26], $column[27], $column[28], $column[29], $column[30], $column[31], $column[32], $column[33], $column[34], $column[35], $column[36], $column[37], $column[38], $column[39], $column[40], $column[41], $column[42], $column[43], $column[44], $column[45], $column[46], $column[47])) {
				print_r($column);
				if (isset($column[1])){                      
					  $c1 	= $column[1];
                        $c2 	= $column[2];
                        $c3		=  $column[3];
                        $c4 	= $column[4];
						$c5	    = $column[5];
						$c6	    = $column[6];
						$c7	    = $column[7];
						$c8	    = $column[8];
						$c9	    = $column[9];
						$c10	= $column[10];
						$c11	= $column[11];
						$c12	= $column[12];
						$c13	= $column[13];
						$c14	= $column[14];
						$c15	= $column[15];
						$c16	= $column[16];
						$c17	= $column[17];
						$c18	= $column[18];
						$c19	= $column[19];
						$c20	= $column[20];
						$c21	= $column[21];
						$c22	= $column[22];
						$c23	= $column[23];
						$c24	= $column[24];
						$c25	= $column[25];
						$c26	= $column[26];
						$c27	= $column[27];
						$c28	= $column[28];
						$c29	= $column[29];
						$c30	= $column[30];
						$c31	= $column[31];
						$c32	= $column[32];
						$c33	= $column[33];
						$c34	= $column[34];
						$c35	= $column[35];
						$c36	= $column[36];
						$c37	= $column[37];
						$c38	= $column[38];
						$c39	= $column[39];
						$c40	= $column[40];
						$c41	= $column[41];
						$c42	= $column[42];
						$c43	= $column[43];
						$c44	= $column[44];
						$c45	= $column[45];
						$c46	= $column[46];
						$c47	= $column[47];
						$insertId = $this->insertUser($c1, $c2, $c3, $c4, $c5, $c6, $c7, $c8, $c9, $c10, $c11, $c12, $c13, $c14, $c15, $c16, $c17, $c18, $c19, $c20, $c21, $c22, $c23, $c24, $c25, $c26, $c27, $c28, $c29, $c30, $c31, $c32, $c33, $c34, $c35, $c36, $c37, $c38, $c39, $c40, $c41, $c42, $c43, $c44, $c45, $c46, $c47);
                        print_r($insertId);
						if (! empty($insertId)) {
                            $output["type"] = "success";
                            $output["message"] = "Import completed.";
                            $importCount ++;
                        }
                    }
                } 
				else {
                    $output["type"] = "error";
                    $output["message"] = "Problem in importing data.";
                }
            }
			print_r($importCount);
            if ($importCount == 0) {
                $output["type"] = "error";
                $output["message"] = "Duplicate data found.";
            }
            return $output;
		}	
	}
	function hasEmptyRow(array $column)
    {
        $columnCount = count($column);
        $isEmpty = true;
        for ($i = 0; $i < $columnCount; $i ++) {
            if (! empty($column[$i]) || $column[$i] !== '') {
                $isEmpty = false;
            }
        }
        return $isEmpty;
    }
	function insertUser($c1, $c2, $c3, $c4, $c5, $c6, $c7, $c8, $c9, $c10, $c11, $c12, $c13, $c14, $c15, $c16, $c17, $c18, $c19, $c20, $c21, $c22, $c23, $c24, $c25, $c26, $c27, $c28, $c29, $c30, $c31, $c32, $c33, $c34, $c35, $c36, $c37, $c38, $c39, $c40, $c41, $c42, $c43, $c44, $c45, $c46, $c47)
    {
		
		$sql = "INSERT INTO `accunetix_format` (`TestD`, `Target`, `Name`, `ModuleName`, `Details`, `RawTextDetails`, `Affects`, `Parameter`, `AOPSourceFile`, `AOPSourceLine`, `AOPAdditional`, `IsFalsePositive`, `Severity`, `Type`, `Impact`, `Description`, `DetailedInformation`, `Recommendation`, `Request`, `CWEList`, `CVEList`, `CVSSDescriptor`, `CVSSScore`, `CVSSAV`, `CVSSAC`, `CVSSC`, `CVSSC2`, `CVSSI`, `CVSSA`, `CVSE`, `CVSSRL`, `CVSSRC`, `CVSS3Descriptor`, `CVSS3Score`, `CVSS3TempScore`, `CVSS3EnvScore`, `CVSS3AV`, `CVSS3AC`, `CVSS3PR`, `CVSS3UI`, `CVSS3S`, `CVSS3C`, `CVSS3I`, `CVSS3A`, `CVSS3E`, `CVSS3RL`, `CVSS3RC`, `ReferenceNameUrl`)
									   values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
							$paramType = "sssssssssssssssssssssssssssssssssssssssssssssss";
							$paramArray = array(
								$c1,
								$c2,
								$c3,
								$c4,
								$c5,
								$c6,
								$c7,
								$c8,
								$c9,
								$c10,
								$c11,
								$c12,
								$c13,
								$c14,
								$c15,
								$c16,
								$c17,
								$c18,
								$c19,
								$c20,
								$c21,
								$c22,
								$c23,
								$c24,
								$c25,
								$c26,
								$c27,
								$c28,
								$c29,
								$c30,
								$c31,
								$c32,
								$c33,
								$c34,
								$c35,
								$c36,
								$c37,
								$c38,
								$c39,
								$c40,
								$c41,
								$c42,
								$c43,
								$c44,
								$c45,
								$c46,
								$c47
							);
            $insertId = $this->conn->insert($sql, $paramType, $paramArray);
        return $insertId;
    }
}
?>