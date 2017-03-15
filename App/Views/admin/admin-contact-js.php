{% if session.user == true and session.access_level == 2 %}
<script>
// initialize editor variable, and it will be used in makePageEditable();
// then its content will be available for use in saveEditedPage()
var editor;

function makePageEditable(item) // called in menu Admin > Edit content
{
  if (($(".editablecontent").length != 0))
  {
      // make buttons visible
      $(".admin-hidden").addClass('admin-display').removeClass('admin-hidden');
      // change onclick function (@topnav.blade.php)
      $(item).attr("onclick","turnOffEditing(this)");
      // change text that displays in drop-down
      $(item).html("Turn off editing");
      // add HTML attribute 'contenteditable' and set to true, making text editable
      // Resource:  http://www.w3schools.com/tags/att_global_contenteditable.asp
      $(".editablecontent").attr("contenteditable","true");
      // add class to
      $(".editablecontent").addClass("outlined");
      // store value of #editablecontent into #old (hidden input field)
      $("#oldcontact").val($("#editablecontactcontent").html());

      var editoroptions =
      {
          allowedContent: true,
          floatSpaceDockedOffsetX: 250,
          floatSpaceDockedOffsetY: 100
      }
      // array that includes all elements with class of 'editablecontent'
      var elements = document.getElementsByClassName( 'editablecontent' );
      // loop through array of elements & turns on CKEditor
      for ( var i = 0; i < elements.length; ++i )
      {
          CKEDITOR.inline( elements[ i ], editoroptions );
      }
      // make editor live - (see CKEDITOR documentation)
      CKEDITOR.on( 'instanceLoaded', function(event)
      {
            // store ckeditor data in variable 'editor'
            editor = event.editor;
      });
  }
  else
  {
      alert ('No editable content on page!');
  }
}

function turnOffEditing(item)
{
  // built-in CKEDITOR function to find every live instance
  for (name in CKEDITOR.instances)
  {
      CKEDITOR.instances[name].destroy()
  }
  // hide save and cancel buttons
  $(".admin-display").addClass('admin-hidden').removeClass('admin-display');
  // change onclick function back to default
  $(".menu-item").attr("onclick","makePageEditable(this)");
  // change drop-down text back to default
  $(".menu-item").html("Edit content");
  // change contenteditable attribute to false
  $(".editablecontent").attr("contenteditable","false");
  // remove outlined class
  $(".editablecontent").removeClass("outlined");

  // if hidden input field -- #old -- has content, replace with null
  if ($('#oldcontact').val() != '')
  {
      $(".editablecontent").html($("#oldcontact").val());
  }
}



function saveEditedPage()
{
  // get data from ckeditor (~ line #85) and store in variable (where does getData come from? -- a CKEDITOR method?)
  var contactpagedata = editor.getData();

  // store data (pagedata) into hidden input field with name & id of "thedata" -- #the data -- (~ line #21)
  $("#thecontactdata").val(contactpagedata);

  // pass option to jQuery form plugin (after ajaxSubmit executes showResponse function)
  //  options is a Javacript object
  var options = { success: showResponse };

  // syntax to submit form (form name id = editpage);
  // 'unbind('submit')' prevents normal form submission like jQuery event.preventDefault()
  // .ajaxSubmit (jQuery form plugin built-in method) Resource: http://malsup.com/jquery/form/#ajaxSubmit
  // .ajaxSubmit "immediately serializes the form  data and sends it to the server." (source: http://jquery.malsup.com/form/#faq)
  $("#editcontactpage").unbind('submit').ajaxSubmit(options);
  //$("#editpage").unbind('submit').ajaxSubmit();

  // don't do anything else
  return false;
}


// Resource:  http://malsup.com/jquery/form/#ajaxSubmit
function showResponse(responseText, statusText, xhr, $form)
{
  //if (responseText == 'OK')
  if (responseText == '')
  {
      $("#oldcontact").val('');
      turnOffEditing();
  }
  else
  {
      alert(responseText);
  }
}
</script>
{% endif %}
