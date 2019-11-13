/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('membre', {
    ID_MEMBRE: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      autoIncrement: true,
      references: {
        model: 'Users',
        key: 'id'
      }
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
      type: DataTypes.STRING(50),
      allowNull: false
    },
    DROIT: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    ID_REGION: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'region',
        key: 'ID_REGION'
      }
    }
  }, {
    timestamps : false,
    tableName: 'membre'
  });
};
