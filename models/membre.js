/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('membre', {
    ID_MEMBRE: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      autoIncrement: true
    },
    NOM: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    PRENOM: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    MAIL: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    PASSWORD: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    actif: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '1'
    },
    ID_REGION: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'region',
        key: 'ID_REGION'
      }
    },
    TYPE_UTILISATEUR: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '1',
      references: {
        model: 'type_utilisateur',
        key: 'TYPE_UTILISATEUR'
      }
    }
  }, {
    timestamps : false,
    tableName: 'membre'
  });
};
