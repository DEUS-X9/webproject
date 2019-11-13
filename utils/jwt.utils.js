//Imports
var jwt = require('jsonwebtoken');

const JWT_SIGN_SECRET = 'fuizyehcdbskuyfz!e';

//Exported functions
module.exports = {
    generateTokenForUser: function(userData) {
        return jwt.sign({
            ID_MEMBRE: userData.id,
            DROIT: userData.DROIT
        },
        JWT_SIGN_SECRET,
        {
            expiresIn: '1h'
        })
    },
    parseAutorization: function(authorisation) {
        return (authorisation != null) ? authorisation.replace('Bearer ','') :null;
    },
    getUseId: function(authorisation) {
        var ID_MEMBRE = -1;
        var token = module.exports.parseAutorization(authorisation);
        if(token != null) {
            try {
              var jwtToken = jwt.verify(token, JWT_SIGN_SECRET);
              if(jwtToken != null)
                ID_MEMBRE = jwtToken.ID_MEMBRE;
            } catch(err) { }
          }
          return ID_MEMBRE;
    }
    
}