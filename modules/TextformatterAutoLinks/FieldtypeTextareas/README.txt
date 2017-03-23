ProcessWire ProFields: Textareas Fieldtype and Inputfield
=========================================================

This is a commercially supported module, please do not distribute. 


WHAT IT DOES
------------

This fieldtype lets you combine multiple named text fields into a single field. 
This can help to reduce the quantity of fields necessary in your ProcessWire
installation, especially when you have several fields that all have the same 
requrements. For instance, if you needed 10 textarea fields that each represented
some different data, but all had the same input requirements, then you could 
convert all of those to 1 Textareas field. 


USAGE EXAMPLE
-------------

Please read this usage example completely, as the example continues to be used
throughout this document. 

Lets say we're building a website for a realtor, and we're going to have a directory 
of homes for sale on this web site. We will need several 'notes' fields that each 
represent different information about the property, but all have similar input 
requirements:

  - Realtor's notes about the property
  - About the company that built the property
  - About the neighborhood that the property is in 
  - About the utilities, water, sewer, etc. 
  - Descriptions for the bedrooms in the home. 

We were planning to set these all up as seperate Textarea fields using a rich text 
editor like CKEditor. But now that we've got the Textareas Fieldtype, we have
another option: we can combine them all into one, while still being able to 
input them separately in your admin, and output them separately on the front-end
of the site. 


HOW TO INSTALL
--------------

1. Copy all the files in this directory to /site/modules/FieldtypeTextareas/ 

2. In your admin, go to Modules > Check for new modules. 

3. Click the "Install" button next to FieldtypeTextareas. 


HOW TO CREATE A TEXTAREAS FIELD
-------------------------------

1. In your admin, go to Setup > Fields > Add New. 

2. Enter a field name and label, and select "Textareas" for the "Type". Save.

3. On the "Details" tab, choose an "Inputfield Type". This will be the type used
   by each of your textareas. If we were to continue our usage example above,
   we would choose one of the rich text editor fields like TinyMCE or CKEditor.

4. Also on the "Details" tab, see "Textarea Definitions". Enter a name=label for
   each of your fields you want represented here. As an example, we would define
   the fields in our usage example above in this manner: 

   property = About the property
   builder = About the company that built the property
   neighborhood = About the neighborhood that the property is in 
   utilities = About the utilities such as water, sewer, etc. 
   bedrooms = Descriptions for the bedrooms in the home. 

5. Save. Then click to the "Input" tab. Depending on what input type you selected
   on the "Details" tab, you may have additional configuration options available 
   to you on this "Input" tab. Make any changes necessary and Save again. 

6. Add your new field you created to one or more templates and start editing
   pages using your new Textareas field! 


HOW TO ACCESS YOUR FIELD FROM THE API
-------------------------------------

Your new Textareas field is represented on your page as an object where you can 
reference any of the individual components from that object directly by name. 

For example, lets say that you named your Textareas field "notes". Continuing
the usage example from above, we could access any of the individual pieces of
data on that field from our $page like this:

    echo "<h2>About the Property</h2>";
    echo $page->notes->property;

    echo "<h2>About the Builder</h2>";
    echo $page->notes->builder; 

    echo "<h2>About the Neighborhood</h2>";
    echo $page->notes->neighborhood; 

...and so on. Note that we included some H2 headlines in our output there. One
thing you can do is to pull in the labels that were defined with the field. If
you wanted to do that, you could output the above like this:

    echo "<h2>" . $page->notes->label('property') . "</h2>";
    echo $page->notes->property; 

Lets say that you just wanted to output all of the data present in your
Textareas field together. Your Textareas field can be iterated like an array:

    foreach($page->notes as $name => $value) {
        $label = $page->notes->label($name); 
        echo "<h2>$label</h2>" . $value; 
    }

You can also get the same result as the above by using the built-in render
method (useful for testing and other quick usages):

    echo $page->notes->render(); // outputs with labels as h2 headlines
    echo $page->notes->render('h3'); // optionally specify headline type
  

HOW TO SET VALUES TO YOUR TEXTAREAS FIELD 
-----------------------------------------

You can set values to your Textareas field directly, in the same manner in
which you access it:

    $page->of(false); // turn of output formatting, if it isn't already. 
    $page->notes->builder = "<p>AAA Build Co. made this house in 1978...</p>";
    $page->save('notes'); 


TEXTAREAS FIELDS AND $pages->find()
-----------------------------------

All the individual fields within your single Textareas field are bundled into
one searchable field. When you perform a search, it will search all of them
together. Continuing the usage example above, lets say that your field is named
"notes" and that you want to search for the text "AAA Build". You must search
the entire "notes" field:

    $items = $pages->find("notes%=AAA Build"); 


SUPPORT AND UPGRADES
--------------------

Please see the ProFields support board at http://processwire.com/talk/. If you
have purchased ProFields and don't have access to the support board, please 
send a PM to Ryan in the forum or email ryan@processwire.com. 




