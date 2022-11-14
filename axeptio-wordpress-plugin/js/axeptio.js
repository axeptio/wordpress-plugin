// noinspection ES6ConvertVarToLetConst

/*


 */

window._axcb = window._axcb || [];
window._axcb.push(function(sdk){

    console.info("axeptioWordpressStep", window.axeptioWordpressStep);

    sdk.on('ready', function(){
        sdk.config.cookies.map(function(cookieConfig){
            cookieConfig.steps.push(window.axeptioWordpressStep);
        })
    });

    sdk.on('cookies:complete', function(choices){
        getAllComments(document.body).forEach(function(comment){
            if (comment.nodeValue.indexOf('axeptio_blocked') > -1){
                var plugin = comment.nodeValue.match(/axeptio_blocked ([\w_-]+)/)[1];
                if(!choices['wp_' + plugin]){
                    return;
                }
                var value = comment.nodeValue.split("\n").slice(1).join("\n");
                var elem = document.createElement('div');
                elem.innerHTML = value;
                comment.parentElement.replaceChild( elem.childNodes[0], comment );
            }
        })
    });

    function filterNone() {
        return NodeFilter.FILTER_ACCEPT;
    }

    function getAllComments(rootElem) {
        var comments = [];
        // Fourth argument, which is actually obsolete according to the DOM4 standard, is required in IE 11
        // noinspection JSCheckFunctionSignatures
        var iterator = document.createNodeIterator(rootElem, NodeFilter.SHOW_COMMENT, filterNone, false);
        var curNode;
        while (curNode = iterator.nextNode()) {
            comments.push(curNode);
        }
        return comments;
    }

})
