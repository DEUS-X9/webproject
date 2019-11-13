/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('evenements', {
    ID_EVENTS: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      autoIncrement: true
    },
    EVENTS: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    E_DESCRIPTION: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    E_IMAGE: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    E_DATE: {
      type: DataTypes.DATEONLY,
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
    tableName: 'evenements'
  });
};
