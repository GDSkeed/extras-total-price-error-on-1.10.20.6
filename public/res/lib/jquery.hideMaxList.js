(function($){
    $.fn.extend({
        hideMaxListItems: function(options)
        {
            // OPTIONS
            var defaults = {
                max: 3,
                speed: 1000,
                itemSelector: 'li',
                moreText:'Show more',
                moreHTML:'<p class="maxlist-more"><span></span></p>', // requires class and child <span>
            };
            var options =  $.extend(defaults, options);

            // FOR EACH MATCHED ELEMENT
            return this.each(function() {
                var $thisList = $(this);
                var op = options;
                var totalListItems = $thisList.children(op.itemSelector).length;
                var speedPerItem;

                // Get animation speed per LI; Divide the total speed by num of LIs.
                // Avoid dividing by 0 and make it at least 1 for small numbers.
                if ( totalListItems > 0 && op.speed > 0  ){
                    speedPerItem = Math.round( op.speed / totalListItems );
                    if ( speedPerItem < 1 ) { speedPerItem = 1; }
                } else {
                    speedPerItem = 0;
                }

                // If list has more than the "max" option
                if ( (totalListItems > 0) && (totalListItems > op.max) )
                {
                    // Initial Page Load: Hide each LI element over the max
                    $thisList.children(op.itemSelector).each(function(index){
                        if ( (index+1) > op.max ) {
                            $(this).hide(0);
                        } else {
                            $(this).show(0);
                        }
                    });

                    // Replace [COUNT] in "moreText" with number of items beyond max
                    var howManyMore = totalListItems - op.max;
                    var newMoreText = op.moreText;

                    if ( howManyMore > 0 ){
                        newMoreText = newMoreText.replace("[COUNT]", howManyMore);
                    }

                    // Add "Read More" button, or unhide it if it already exists
                    if ( $thisList.next(".maxlist-more").length > 0 ){
                        $thisList.next(".maxlist-more").show();
                    } else {
                        $thisList.after(op.moreHTML);
                    }

                    // READ MORE - add text within button, register click event that slides the items up and down
                    $thisList.next(".maxlist-more")
                        // .children("span")
                        .html(newMoreText)
                        .off('click')
                        .on("click", function(e){
                            var $theLink = $(this);

                            // Get array of children past the maximum option
                            // var listElements = $theLink.parent().prev("ul, ol").children(op.itemSelector);
                            // listElements = listElements.slice(op.max);

                            var listElements = $thisList.children(op.itemSelector+':hidden'),
                                elementsCount = $(listElements).length;

                            listElements = listElements.slice(0, op.max);

                            // Sequentially slideToggle the list items
                            // For more info on this awesome function: http://goo.gl/dW0nM

                            if (elementsCount > 0) {
                                var i = 0;
                                (function() { $(listElements[i++] || []).slideToggle(speedPerItem,arguments.callee); })();
                            }

                            if (elementsCount <= op.max)
                                $theLink.hide();

                            // Prevent Default Click Behavior (Scrolling)
                            e.preventDefault();
                        });
                }
                else {
                    // LIST HAS LESS THAN THE MAX
                    // Hide "Read More" button if it's there
                    if ( $thisList.next(".maxlist-more").length > 0 ){
                        $thisList.next(".maxlist-more").hide();
                    }
                    // Show all list items that may have been hidden
                    $thisList.children(op.itemSelector).each(function(index){
                        $(this).show(0);
                    });
                }
            });
        }
    });
})(jQuery); // End jQuery Plugin