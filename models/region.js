/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('region', {
    ID_REGION: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      autoIncrement: true
    },
    REGION: {
      type: DataTypes.STRING(50),
      allowNull: false
    }
  }, {
    tableName: 'region'
  });
};
