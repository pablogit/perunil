<?php
$this->pageTitle = Yii::app()->name;
switch ($searchtype) {
    case 'adv':
        $titre = "avancée";
        $dp = 'adv_dp';
        $adp = 'adv_adp';
        $count = 'adv_count';
        $search_url = 'site/advSearch';
        $clean_url = 'site/advclean';
        break;

    case 'admin':
        $titre = "admin";
        $dp = 'admin_dp';
        $adp = 'admin_dp';
        $count = 'admin_count';
        $search_url = 'admin/search';
        $clean_url = 'admin/searchclean';
        break;

    default: // simple
        $titre = "simple";
        $dp = 'simple_dp';
        $adp = 'simple_adp';
        $count = 'simple_sql_query_count';
        $search_url = 'site/index';
        $clean_url = 'site/simpleclean';
        break;
}
?>
<h2>Résultats de la recherche <?php echo $titre; ?></h2>

<div style="margin-bottom: 1em;">
    <?php
    echo CHtml::htmlButton('<span class="glyphicon glyphicon-backward"> </span> Modifier les critères de recherche', array(
        'onclick' => 'js:document.location.href="' . Yii::app()->createUrl($search_url) . '"',
        'class' => "btn btn-default  btn-sm"));
    echo " ";
    echo CHtml::htmlButton('Nouvelle recherche', array(
        'onclick' => 'js:document.location.href="' . Yii::app()->createUrl($clean_url) . '"',
        'class' => "btn btn-primary  btn-sm"));
    echo " ";
    echo CHtml::htmlButton('Affichage liste', array(
        'onclick' => 'js:document.location.href="' . Yii::app()->request->requestUri . '&typeAffichage=1"',
        'class' => "btn btn-warning  btn-sm"));
    echo " ";
    echo CHtml::htmlButton('Affichage tableau', array(
        'onclick' => 'js:document.location.href="' . Yii::app()->request->requestUri . '&typeAffichage=2"',
        'class' => "btn btn-warning  btn-sm"));
    ?>
</div>

<?php
// On affiche la zone des résultats uniquement si un recherche existe. 
if ($search_done) {
    if (!Yii::app()->user->isGuest){
        $this->renderPartial('/admin/_adminSearchButton');
        if (Yii::app()->session['search']->admin_affichage == 'abonnement') {
            // Affichage par abonnement
            $this->renderPartial('/admin/_aboSearchResults', array('dataProvider' => Yii::app()->session['search']->$adp));
        }
    }
    
    ?>

    <table class="table table-responsive">
        <thead> <!-- En-tête du tableau -->
            <tr>
                <th>#</th>
                <th>Titre</th>
                <th>Lien</th>
                <th>Etat de collection</th>
                <th>Plateforme/Localisation</th>
                <th></th>
            </tr>
        </thead>
        <tbody> <!-- Corps du tableau -->
            <?php
            // Affichage des résultat pour le publique
            if (Yii::app()->user->isGuest) {
                $this->widget('zii.widgets.CListView', array(
                    'dataProvider' => Yii::app()->session['search']->$dp,
                    'itemView' => '_view_tab',
                    'ajaxUpdate' => false,
                    'template' => "{pager}\n{items}\n",
                ));
            }
            elseif (Yii::app()->session['search']->admin_affichage == 'journal') {
                    $this->widget('AdminCListView', array(
                        'dataProvider' => Yii::app()->session['search']->$dp,
                        'itemView' => '/site/_view_tab',
                        'ajaxUpdate' => false,
                        'template' => "{pager}\n{items}\n",
                    ));
            }
            ?>
        </tbody>
    </table>

    <?php
    // Affichage du message des résulats de la recherche
    Yii::app()->session['totalItemCount'] = Yii::app()->session['search']->$count;
    $msg = "";
    if (Yii::app()->session['search']->maxresults > 0 && $searchtype == "simple") { // Il y a une limitation du nombre de résultats
        $msg = ", limitée à " . Yii::app()->session['search']->maxresults . " résultats,";
    }
    Yii::app()->user->setFlash('success', "Votre requête$msg a retourné " .
            Yii::app()->session['totalItemCount'] .
            " résultat(s).<br/>" .
            Yii::app()->session['search']->getQuerySummary());
} else {
    //
    // Si aucune recherche, on affiche les logo des plateformes
    // 
    ?>
    <p>Il n'y a aucun résultat à afficher.</p>


    <?php
    $this->renderPartial('_logosPlateforme');
}
?>
