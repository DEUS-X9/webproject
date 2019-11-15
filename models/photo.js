/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('photo', {
    ID_PHOTO: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      autoIncrement: true
    },
    CHEMIN: {
      type: DataTypes.STRING(70),
      allowNull: false
    },
    DATE: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    ID_MEMBRE: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'membre',
        key: 'ID_MEMBRE'
      }
    }
  }, {
    timestamps : false,
    tableName: 'photo'
  });
};
