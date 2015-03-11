'use strict';

/**
 * Namespace for ULTIMATE.Content
 *
 * @namespace
 */
ULTIMATE.ACL = {};

/**
 * Extends WCF ACL support with ability to use ACL in frontend.
 *
 * @author      Alexander Ebert
 * @copyright   2011-2015 Jim Martens
 * @license     http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 */
ULTIMATE.ACL.List = WCF.ACL.List.extend({
    /**
     * Loads current ACL configuration.
     */
    _loadACL: function() {
        this._proxy.setOption('data', {
            actionName: 'loadAll',
            className: 'ultimate\\data\\acl\\option\\UltimateACLOptionAction',
            parameters: {
                categoryName: this._categoryName,
                objectID: this._objectID,
                objectTypeID: this._objectTypeID
            }
        });
        this._proxy.sendRequest();
    }
});
