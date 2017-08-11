<?php
if (!class_exists('WP_List_Table_Copy')) {
    require_once(plugin_dir_path(__FILE__) . 'class-wp-list-table-copy.php');
}
class Fbreview_List_Table extends WP_List_Table_Copy {

    var $example_data = array();

    function __construct() {
        global $status, $page;
        global $wpdb;
        $Lists = $wpdb->get_results('SELECT * FROM  ' . $wpdb->prefix . 'fbrev_page_review');
        //echo $wpdb->last_query;
        $i = 0;
        foreach ($Lists as $List) {
            $this->example_data[$i]['id'] = $List->id;
            $this->example_data[$i]['page_id'] = stripcslashes($List->page_id);
            $this->example_data[$i]['rating'] = stripcslashes($List->rating);
            $this->example_data[$i]['text'] = stripcslashes($List->text);
            $this->example_data[$i]['time'] = stripcslashes($List->time);
            $this->example_data[$i]['author_id'] = stripcslashes($List->author_id);
            $this->example_data[$i]['author_name'] = stripcslashes($List->author_name);
            $this->example_data[$i]['tag'] = stripcslashes($List->tag);
            $status = ($List->deleted == 1)?'Invisible':'Visible';
            $color = ($List->deleted == 1)?'red':'green';

            if ($_GET['paged'] != '') {
                $actions = array(
                    'edit' => sprintf('<a href="javascript:void(0);" onclick="quickEdit(\'%s\',\'%s\',this)">Quick edit</a>', $List->tag, $List->id),
                    'change_status' => sprintf('<a href="?page=%s&action=%s&fbrev=%s&paged=%s" style="color:'.$color.'">'.$status.'</a>', 'wps-facebook-review-status', 'change_status', $List->id, $_GET['paged']),
                );
            } else {
                $actions = array(
                    'edit' => sprintf('<a href="javascript:void(0);" onclick="quickEdit(\'%s\',\'%s\',this)">Quick edit</a>', $List->tag, $List->id),
                    'change_status' => sprintf('<a href="?page=%s&action=%s&fbrev=%s" style="color:'.$color.'">'.$status.'</a>', 'wps-facebook-review-status', 'change_status', $List->id),
                );
            }

            $actions = sprintf('%1$s %2$s', $item['id'], $this->row_actions($actions));
            $this->example_data[$i]['Action'] = $actions;
            $i++;
        }
        //echo '<pre>'; print_r($this->example_data);
        parent::__construct(array(
            'singular' => __('fbreview', 'cpqarealisttable'), //singular name of the listed records
            'plural' => __('fbreviews', 'cpqarealisttable'), //plural name of the listed records
            'ajax' => false        //does this table support ajax?
        ));

        add_action('admin_head', array(&$this, 'admin_header'));
    }

    function admin_header() {
        $page = ( isset($_GET['page']) ) ? esc_attr($_GET['page']) : false;
        if ('my_list_test' != $page)
            return;
        echo '<style type="text/css">';
        echo '.wp-list-table .column-id { width: 5%; }';
        echo '.wp-list-table .column-booktitle { width: 40%; }';
        echo '.wp-list-table .column-author { width: 35%; }';
        echo '.wp-list-table .column-isbn { width: 20%;}';
        echo '</style>';
    }

    function no_items() {
        _e('No Counties found, dude.');
    }

    function column_default($item, $column_name) {
        switch ($column_name) {
            case 'page_id':
            case 'Action':
                return $item[$column_name];
            default:
                // return print_r($item, true); //Show the whole array for troubleshooting purposes
                return $item[$column_name];
        }
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'page_id' => array('page_id', false)
        );
        return $sortable_columns;
    }

    function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'page_id' => __('Page Id', 'cpqarealisttable'),
            'rating' => __('Rating', 'cpqarealisttable'),
            'text' => __('Text', 'cpqarealisttable'),
            'time' => __('Time', 'cpqarealisttable'),
            'author_id' => __('Author Id', 'cpqarealisttable'),
            'author_name' => __('Author name', 'cpqarealisttable'),
            'tag' => __('Tag', 'cpqarealisttable'),
            'Action' => __('Action', 'cpqarealisttable'),
        );
        return $columns;
    }

    function usort_reorder($a, $b) {
        // If no sort, default to title
        $orderby = (!empty($_GET['orderby']) ) ? $_GET['orderby'] : 'id';
        // If no order, default to asc
        $order = (!empty($_GET['order']) ) ? $_GET['order'] : 'asc';
        // Determine sort order
        $result = strcmp($a[$orderby], $b[$orderby]);
        // Send final sort direction to usort
        return ( $order === 'asc' ) ? $result : -$result;
    }

      function get_bulk_actions() {
      $actions = array(
      'change_status_bulk' => 'Change Status'
      );
      return $actions;
      }

      function process_bulk_action() {

      //Detect when a bulk action is being triggered...
        // If the delete bulk action is triggered
        if (  isset( $_POST['action'] ) && $_POST['action'] == 'change_status_bulk' )
        {

          $delete_ids = esc_sql( $_POST['change_status_bulk'] );
          global $wpdb;
          // loop over the array of record IDs and delete them
          foreach ( $delete_ids as $id ) {
            $sql = $wpdb->prepare("select *  from ".$wpdb->prefix."fbrev_page_review where id= %s",$id);
            $res = $wpdb->get_row($sql, ARRAY_A);
            if ($res['deleted'] == 1) {
                $wpdb->update($wpdb->prefix."fbrev_page_review", array('deleted'=>0), array('id'=>$id));
            } else {
                $wpdb->update($wpdb->prefix."fbrev_page_review", array('deleted'=>1), array('id'=>$id));
            }
          }

          wp_redirect( esc_url( add_query_arg() ) );
          exit;
        }
      }

    function column_cb($item) {
        return sprintf(
                 '<input type="checkbox" name="%1$s[]" value="%2$s" />', 
                /* $1%s */ $this->_args['singular'], //Let's simply repurpose the table's singular label ("movie")
                /* $2%s */ $item['id']                //The value of the checkbox should be the record's id
        );
    }

    function prepare_items() {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        usort($this->example_data, array(&$this, 'usort_reorder'));

        $per_page = 5;
        $current_page = $this->get_pagenum();
        $total_items = count($this->example_data);

        // only ncessary because we have sample data
        $this->found_data = array_slice($this->example_data, ( ( $current_page - 1 ) * $per_page), $per_page);

        $this->set_pagination_args(array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page                     //WE have to determine how many items to show on a page
        ));
        $this->items = $this->found_data;
    }

    function quickedit()
    {   
        ?>
        <script type="text/javascript">
            function quickEdit(tag, id, e){
                
                html = '';
                 html+='<tr id="quick_edit_row">';
                 html+='<td colspan="6"></td>';
            html+='<td colspan="2">';
                html+='<div>';
                    html+='<input type="text" name="tag" value="" id="edit_tag">';
                    html+='<button onclick="cancel_quickedit()" class="button">Cancel</button>';
                    html+="<button onclick='";
                    html+="save_quickedit(\""+id+"\",this)";
                    html+="' class='button'>Save</button>";
                html+='</div>';
            html+='</td>';    
        html+='</tr>';
                jQuery('#quick_edit_row').remove();
                jQuery(e).parents('tr').after(html);
                jQuery('#edit_tag').val(jQuery(e).parents('tr').find('.column-tag').text());
                
            }
            function cancel_quickedit(){
                jQuery('#quick_edit_row').remove();
            }
            function save_quickedit(id,e){
                jQuery.post(ajaxurl,{'id':id,'tag':jQuery('#edit_tag').val(),'action': 'update_fb_tag',}, function(data){
                    if(data.type == 'success') {
                        jQuery(e).parents('tr').prev().find('.column-tag').text(jQuery('#edit_tag').val());
                        jQuery('#quick_edit_row').remove();
                    }
                },'json')
            }

        </script>
    <?php    
    }

}

//class