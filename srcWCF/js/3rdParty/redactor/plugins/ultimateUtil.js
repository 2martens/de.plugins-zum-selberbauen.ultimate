if (!RedactorPlugins) var RedactorPlugins = {};

/**
 * Provides a way to set text that is then synchronized with the Redactor editor.
 *
 * @author		Jim Martens
 * @copyright	2011-2014 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 */

RedactorPlugins.ultimateUtil = {
    /**
     * Initializes the RedactorPlugins.ultimateUti plugin.
     */
    init: function() {
        
    },
    
    /**
     * Sets text using BBCodes.
     *
     * @param   {String}    text
     */
    setText: function(text) {
        this.$source.val(text);
        
        if (this.inWysiwygMode()) {
            this._convertToHtml();
            this.modified = '';
            this.toggleVisual();
            this._observeQuotes();
        }
    }
};
