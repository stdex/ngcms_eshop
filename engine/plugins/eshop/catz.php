<?php
if (!defined('NGCMS')) die ('HAL');

//get spaces to list subcategories
function get_prefix($CategoryID)
{
    global $mysql, $config;
    
    $query = mysql_query('
                SELECT parent_id 
                FROM Categories 
                WHERE CategoryID='.$CategoryID);
                
    $query = mysql_fetch_assoc($query);
    $ParentID = $query['ParentID'];
    
    echo '&nbsp;&nbsp;&nbsp;'; 
    {
        if ($ParentID == 0) 
        { 
            $add_prefix .= ''; 
        }
        else
        {
            $add_prefix .= '<img src="images/tree.gif">&nbsp;&nbsp;&nbsp;';         
            $query2 = mysql_query('
                        SELECT * 
                        FROM Categories 
                        WHERE CategoryID='.$ParentID);
            while ( $row2 = mysql_fetch_assoc($query2) )
            {
                $CategoryID2 = $row2['CategoryID'];         
                $ParentID2 = $row2['ParentID'];
            }   
            get_prefix($CategoryID2);
        }
    }   
    return $add_prefix;
}

//function that lists categories
function generate_menu($parent)
{
    global $mysql, $config;
    
    $has_childs = false;

    //use global array variable
    global $menu_array;
    
    $addspaces = '';        
    
    foreach($menu_array as $key => $value)
    {
        if ($value['parent'] == $parent) 
        {       
        
            if ($value['Description'] != "") 
                $Add_Popups = "<span>".$value['Description']."</span>";
            else 
                $Add_Popups = "";
            
            echo "          
                <tr class=\"GridRowCategories\" onmouseover=\"this.className='GridRowOverCategories'\"  onmouseout=\"this.className='GridRowCategories'\">
                <td>
                    <center>" . $value['CategoryID'] . "</center>
                </td>
                <td><span class='popups'>";
                echo get_prefix($value['CategoryID']);
                echo '<img width="16px" height="16px" src="./'.$value['IconFile'].'"> '.$value['CategoryName'] . $Add_Popups . "</span></td>
                <td>";
                echo get_prefix($value['CategoryID']);
                echo $value['SortOrder'] . "
                    <a title=\"Up\" href=\"categories.php?ToDo=sortCategory&sortOrder=" . $value['SortOrder'] . "&sortField=up&categoryID=" . $value['CategoryID'] . "&categoryName=" . $value['CategoryName'] . "\"><img src='images/sortup.gif' border=\"0\"></a>
                    <a title=\"Down\" href=\"categories.php?ToDo=sortCategory&sortOrder=" . $value['SortOrder'] . "&sortField=down&categoryID=" . $value['CategoryID'] . "&categoryName=" . $value['CategoryName'] . "\"><img src='images/sortdown.gif' border=\"0\"></a>
                </td>
                <td>
                    <center>" . $value['ShowCatInMainMenu'] . "</center>
                </td>
                <td>
                      <a title='Create New Category' class='CategoryEditClass' href='categories.php?ToDo=createCategory&newCategoryId=" . $value['CategoryID'] . "'>New</a>&nbsp;&nbsp  
                      <a title='Edit this account' class='CategoryEditClass' href='categories.php?ToDo=editCategory&categoryId=" . $value['CategoryID'] . "'>Edit</a>&nbsp;&nbsp
                      <a title='Delete this category' class='CategoryDeleteClass' href='categories.php?ToDo=deleteCategory&categoryID=" . $value['CategoryID'] . "'>Delete</a><br>
                      </td>
                </tr>
            ";

            if ($key != 0) $addspaces .= '&nbsp;';
            
            //call function again to generate list for subcategories belonging to current category
            generate_menu($key);
        }
    }
}

/*
//function that lists categories and subcategories for Select options. Used on create new category.
function list_categories($parent,$newCategoryId)
{
    global $menu_array;
    
    $addspaces = '';
    
    foreach($menu_array as $key => $value)
    {
        if ($value['parent'] == $parent) 
        {       
            if ($newCategoryId == $value['CategoryID'])
            { 
                echo "<option value='".$value['CategoryID']."' selected>";
                echo get_prefix($value['CategoryID']);
                echo $value['CategoryName']."</option>"; 
            }
            else 
            { 
                echo "<option value='".$value['CategoryID']."'>";
                echo get_prefix($value['CategoryID']);
                echo $value['CategoryName']."</option>"; 
            }
            //call function again to generate list for subcategories
            list_categories($key,$newCategoryId);
        }
    }
}

// create new category 
function SaveCategory($categoryname,$parentcategory,$categorydescription,$sortorder,$showcatinmainmenu,$iconfile)
{
    $target_path = "icons/";
    $target_path = $target_path . ( mysql_insert_id() + 1 ) .'_'. $_FILES['iconfile']['name']; 

    if(move_uploaded_file($_FILES['iconfile']['tmp_name'], $target_path)) 
    {
        $queryEditCategory = "
            UPDATE Categories 
            SET ParentID=$parentcategory, Name='$categoryname', 
                Description='$categorydescription', SortOrder='$sortorder', 
                ShowcatInMainMenu='$showcatinmainmenu', IconName='$target_path'
            WHERE CategoryID=$categoryid";      
    } 
    else
    {
        $target_path = "icons/directory.png";
        $queryEditCategory = "
            UPDATE Categories 
            SET ParentID=$parentcategory, Name='$categoryname', 
                Description='$categorydescription', SortOrder='$sortorder', 
                ShowcatInMainMenu='$showcatinmainmenu'
            WHERE CategoryID=$categoryid";      
    }


    $queryCreateCategory = "
        INSERT INTO Categories (ParentID,Name,Description,SortOrder,ShowcatInMainMenu,IconName) 
        VALUES ($parentcategory,'$categoryname','$categorydescription',$sortorder,$showcatinmainmenu,'$target_path')
        ";
        
    mysql_query($queryCreateCategory) or die('Invalid query: ' . mysql_error());//die('Error, query failed');




    // if category was created, then show a message
    echo "   
        <div>
        <table border='0' cellspacing='0' cellpadding='0' style='padding-top:5px; padding-bottom: 5px;'>
            <tr>
                <td class='Message' width='20' style='background-color: #E7FDE1'><img src='images/success.gif'  hspace='10' vspace='5'></td>
                <td class='Message' width='100%' style='background-color: #E7FDE1'>Category \"<strong>$categoryname</strong>\" was created successfully.</td>
            </tr>
        </table>
        </div>
        ";  
}

// Edit category           
function editExistingCategory($categoryname,$parentcategory,$categorydescription,$sortorder,$showcatinmainmenu,$categoryid,$iconfile)
{
    $target_path = "icons/";
    $target_path = $target_path . ( mysql_insert_id() + 1 ) .'_'. $_FILES['iconfile']['name']; 

    if(move_uploaded_file($_FILES['iconfile']['tmp_name'], $target_path)) 
    {
        $queryEditCategory = "
            UPDATE Categories 
            SET ParentID=$parentcategory, Name='$categoryname', 
                Description='$categorydescription', SortOrder='$sortorder', 
                ShowcatInMainMenu='$showcatinmainmenu', IconName='$target_path'
            WHERE CategoryID=$categoryid";      
    } 
    else
    {
        $target_path = "icons/directory.png";
        $queryEditCategory = "
            UPDATE Categories 
            SET ParentID=$parentcategory, Name='$categoryname', 
                Description='$categorydescription', SortOrder='$sortorder', 
                ShowcatInMainMenu='$showcatinmainmenu'
            WHERE CategoryID=$categoryid";      
    }

    mysql_query($queryEditCategory) or die('Invalid query: ' . mysql_error());//die('Error, query failed');

    // if category was updated, then show a message
    echo "   
        <div>
        <table border='0' cellspacing='0' cellpadding='0' style='padding-top:5px; padding-bottom: 5px;'>
            <tr>
                <td class='Message' width='20' style='background-color: #E7FDE1'><img src='images/success.gif'  hspace='10' vspace='5'></td>
                <td class='Message' width='100%' style='background-color: #E7FDE1'>Category \"<strong>$categoryname</strong>\" was updated successfully.</td>
            </tr>
        </table>
        </div>
        ";  
}

// Delete category         
function DeleteCategory($categoryid)
{
    //get categoryid and categoryname
    $getCategoryName="
        SELECT CategoryID, Name 
        FROM Categories 
        WHERE CategoryID=".$categoryid;
    $categoryname = mysql_fetch_assoc(mysql_query($getCategoryName));
    $categoryname   = $categoryname["Name"];

    // delete categoryname from DB
    $queryDeleteCategory = "
        DELETE 
        FROM Categories 
        WHERE CategoryID=".$categoryid;
        
    mysql_query($queryDeleteCategory) or die('Invalid query: ' . mysql_error());

    // delete empty subcategories
    $queryDeleteEmptyCategories = "
        SELECT CategoryID 
        FROM Categories 
        WHERE ParentID <> 0 and ParentID 
        NOT IN (SELECT CategoryID FROM Categories)
        ";
        
    $query = mysql_query($queryDeleteEmptyCategories);
    while ( $row = mysql_fetch_assoc($query) )
    {
        $queryDeleteCategory = "
            DELETE 
            FROM Categories 
            WHERE CategoryID=".$row['CategoryID'];
            
        mysql_query($queryDeleteCategory) or die('Invalid query: ' . mysql_error());
    }   
    
    // if category was deleted, then show a message
    echo "   
        <div>
        <table border='0' cellspacing='0' cellpadding='0' style='padding-top:5px; padding-bottom: 5px;'>
            <tr>
                <td class='Message' width='20' style='background-color: #E7FDE1'><img src='images/success.gif'  hspace='10' vspace='5'></td>
                <td class='Message' width='100%' style='background-color: #E7FDE1'>Category \"<strong>$categoryname</strong>\" was deleted successfully.</td>
            </tr>
        </table>
        </div>
        ";  
    //exit();

}

// Sort categories and subcategories       
function SortCategory($catId,$way,$categoryname,$sortorder)
{
    $sortorder = (int)$sortorder;

    // get the category info
    $cat = "
        SELECT CategoryID, ParentID 
        FROM Categories 
        WHERE CategoryID=".$catId;
        
    $cat = mysql_fetch_assoc(mysql_query($cat));

    // now get all the categories with the same parent id so we can sort them all out
    $query = "
        SELECT * 
        FROM Categories 
        WHERE ParentID=".$cat["ParentID"] ." 
        ORDER BY SortOrder ASC, Name ASC";
    $query = mysql_query($query); 

    // load them all into an array in their current sortorder
    while($row = mysql_fetch_array($query))
    {
        $CatList[] = $row["CategoryID"];
    }

    // find the key of our category that we're moving
    $key = array_search($catId, $CatList);

    // determine what other element our category needs to swap with
    if ($way == "up") 
    {
        $swapKey = $key - 1;
    } 
    else 
    {
        $swapKey = $key + 1;
    }

    // if the one we want to swap with exists, then lets swap it
    if(isset($CatList[$swapKey]))
    {
        $swapId = $CatList[$swapKey];
        $CatList[$swapKey] = $catId;
        $CatList[$key] = $swapId;
    }

    $NewCatList = array();

    // clean up the keys so it starts from zero every time
    foreach($CatList as $thisKey=>$thisValue)
    {
        $NewCatList[] = $thisValue;
    }

    // update each of the categories with their new sort order
    foreach($NewCatList as $thisKey=>$thisValue)
    {
        $InputData = array();
        $InputData["SortOrder"] = $thisKey;
        $where = " WHERE CategoryID=" . $thisValue;

        $queryUpdateSortCategory ="
            UPDATE Categories 
            SET SortOrder=".$thisKey." ".$where;

        mysql_query($queryUpdateSortCategory) or die('Invalid query: ' . mysql_error());
    }

    // if categories was sorted, then show a message
    echo "   
        <div>
        <table border='0' cellspacing='0' cellpadding='0' style='padding-top:5px; padding-bottom: 5px;'>
            <tr>
                <td class='Message' width='20' style='background-color: #E7FDE1'><img src='images/success.gif'  hspace='10' vspace='5'></td>
                <td class='Message' width='100%' style='background-color: #E7FDE1'>Category \"<strong>$categoryname</strong>\" was moved \"$way\" successfully.</td>
            </tr>
        </table>
        </div>
        ";  
}

    //get all rows
    $query = mysql_query('
        SELECT * 
        FROM Categories 
        WHERE ShowCatInMainMenu = 1     
        ORDER BY SortOrder, Name
        ');
        
    while ( $row = mysql_fetch_assoc($query) )
    {
        $menu_array[$row['CategoryID']] = 
            array('name' => $row['Name'],
                'parent' => $row['ParentID'],
                'CategoryID' => $row['CategoryID'],
                'CategoryName' => $row['Name'],
                'Description' => $row['Description'],
                'SortOrder' => $row['SortOrder'],
                'ShowCatInMainMenu' => $row['ShowCatInMainMenu'],
                'IconFile' => $row['IconName']);
    }

//recursive function that prints categories for frontend menu
function frontend_menu($parent,$first_time)
{
    $has_childs = false;
    //this prevents printing 'ul' if we don't have subcategories for this category
    
    global $menu_array;
    //use global array variable instead of a local variable to lower stack memory requierment
    foreach($menu_array as $key => $value)
    {
        if ($value['parent'] == $parent) 
        {       
            //if this is the first child print '<ul>'                       
            if ($has_childs === false)
            {
                    //don't print '<ul>' multiple times                             
                    $has_childs = true;
                    if ($first_time == 1)
                    {
                        echo '<ul id="sidebarmenu1">';
                    }                   
                    else echo '<ul>';
            }
            echo '<li><a href="/category/' . $value['name'] . '/">' . $value['name'] . '</a>';
            frontend_menu($key,0);
            //call function again to generate nested list for subcategories belonging to this category
            echo '</li>';
        }
    }
    if ($has_childs === true) echo '</ul>';
}

*/
