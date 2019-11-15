/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('loging', {
    id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      autoIncrement: true
    },
    ip: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    email: {
      type: DataTypes.STRING(255),
      allowNull: false
    },
    login: {
      type: DataTypes.INTEGER(1),
      allowNull: false
    },
    heure: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    timestamps : false,
    tableName: 'loging'
  });
};
