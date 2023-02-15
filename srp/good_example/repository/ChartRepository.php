<?php 

class ChartRepository
{
 public funtion getChartData($data)
 {
   $criteria = new CDbCriteria;
    $criteria->with = array('partner', 'utasok', 'ut', 'transactions', 'szhot', 'szamlat', 'szamlau', 'dijt', 'szlaf', 'szlai');
    $criteria->group = 'SUBSTRING(t.tn_szam, 1, 8), t.p_valuta';
    $criteria->compare('t.id', $data['id']);
    $criteria->compare('t.reg_mod', $data['reg_mod']);
    $criteria->compare('t.tn_szam', $data['tn_szam']);
    $criteria->compare('t.poz_szam', $data['poz_szam'], true, 'AND', false);
    $criteria->compare('t.poz_turn', $data['poz_turn'], true);
    $criteria->compare('t.ut_kod', $data['ut_kod'], true);
    $criteria->compare('t.plus_turn', $data['plus_turn']);
    $criteria->compare('t.ki_date', $data['ki_date'], true);
    $criteria->compare('t.be_date', $data['be_date'], true);
    $criteria->compare('t.tour_oper', $data['tour_oper'], true);
    if ( $data['poziLike'] != '' ) {
      $criteria->addCondition('t.poz_szam LIKE :poziLike');
      $criteria->params[':poziLike'] = $data['poziLike'];
    }
    if ( $data['poziNotLike'] != '' ) {
      $criteria->addCondition('t.poz_szam NOT LIKE :poziNotLike');
      $criteria->params[':poziNotLike'] = $data['poziNotLike'];
    }

    if ( $data['whoSold'] == 1 ) {
      $criteria->with[] = 'partner';
      $criteria->addCondition('partner.p_cegnev1 LIKE :partner');
      $criteria->params[':partner'] = '%test_firm%';
    }
    if ( $data['jelDateClean'] != null ) {
      $criteria->addCondition('substring(t.tn_szam, 1, 8) LIKE \'%' . str_replace('-', '', $data['jelDateClean']) . '%\'');
    }

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination'=>false,
    ));
 }
  
  public function getMonthlyChartData($data)
  {
    $criteria = new CDbCriteria;
    $criteria->with = array('partner', 'utasok', 'ut', 'transactions', 'szhot', 'szamlat', 'szamlau', 'dijt', 'szlaf', 'szlai');
    $criteria->group = 'SUBSTRING(t.tn_szam, 1, 6), t.p_valuta';
    $criteria->compare('t.id', $data['id']);
    $criteria->compare('t.reg_mod', $data['reg_mod']);
    $criteria->compare('t.tn_szam', $data['tn_szam']);
    $criteria->compare('t.poz_szam', $data['poz_szam'], true, 'AND', false);
    $criteria->compare('t.poz_turn', $data['poz_turn'], true);
    $criteria->compare('t.ut_kod', $data['ut_kod'], true);
    $criteria->compare('t.plus_turn', $data['plus_turn']);
    $criteria->compare('t.ki_date', $data['ki_date'], true);
    $criteria->compare('t.be_date', $data['be_date'], true);
    $criteria->compare('t.tour_oper', $data['tour_oper'], true);
    if ( $data['jelDateClean'] != null ) {
      $criteria->addCondition('substring(t.tn_szam, 1, 8) LIKE \'%' . str_replace('-', '', $data['jelDateClean']) . '%\'');
    }
    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination'=>false,
    ));
  }
}
