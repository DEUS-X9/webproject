// Imports
var bcrypt = require('bcrypt');
var jwt = require('jsonwebtoken');
var models = require('../models');

//Routes
module.exports = {
    register:function(req, res) {
        // Params
        var MAIL = req.body.MAIL;
        var NOM = req.body.NOM;
        var PRENOM = req.body.PRENOM;
        var PASSWORD = req.body.PASSWORD;

        if (MAIL == null || NOM == null || PRENOM == null || PASSWORD == null) {
            return res.status(400).json({ 'error': 'missing parameters' });
        }

        models.Membre.findOne({
            attributes: ['MAIL'],
            where: { MAIL: MAIL}
        })
        .then(function(userFound) {
            if (!userFound) {

                bcrypt.hash(PASSWORD, 5, function(err, bcryptedPassword){
                    var newUser = models.Membre.create({
                        MAIL: MAIL,
                        NOM: NOM,
                        PRENOM: PRENOM,
                        PASSWORD: bcryptedPassword,
                        DROIT: 0
                    })
                    .then(function(newUser){
                        return res.status(201).json({
                            'ID_MEMBRE': newUser.id
                        })
                    })
                    .catch(function(err){
                        return res.status(500).json({'error':'cannot add user'});
                    });
                });

            }else{
                return res.status(409).json({ 'error': 'user already exist'})
            }
        })



    },
    login:function(req, res) {

    },
}