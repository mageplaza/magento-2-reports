# Magento 2 Reports by Mageplaza

**Mageplaza Reports for Magento 2** is a free extension which helps stores quickly access to advanced reports on Dashboard. As your shop grows, so is the amount of numbers you have to deal with everyday. Eventually, it would reach a point where you find yourself in dire need of a tool that can take care of the figures for you.

To facilitate the management of statistics, we proudly introduce **Magento 2 Reports extension** by Mageplaza. This module would help store admins keep their number neat and clean while enable them to see the level of progress made in comparison to the same period last month, last year and so on.


[![Latest Stable Version](https://poser.pugx.org/mageplaza/module-reports/v/stable)](https://packagist.org/packages/mageplaza/module-reports)
[![Total Downloads](https://poser.pugx.org/mageplaza/module-reports/downloads)](https://packagist.org/packages/mageplaza/module-reports)



## 1. Documentation
- [Installation guide](https://www.mageplaza.com/install-magento-2-extension/)
- [User guide](https://github.com/magepages/mpdocs/blob/master/docs/reports/index.md)

## 2. FAQs

Q: I got an error: Mageplaza_Core has been already defined

A: Read solution [here](https://github.com/mageplaza/module-core/issues/3)

Q: Can Reports be used with multiple stores? If so, how can I use it?

A: Yes, it can.
* Stores are set the default as the Default Configuration.
* To change the configuration for each store, need to remove tick at Use Website on the right of each option.
* Config of the extension in each store will be applied in the store itself.
* Config in this store doesn't affect the config in the other store.

## 3. How to install Reports extension

### ✓ Install via composer (recommend)
Run the following command in Magento 2 root folder:

```
composer require mageplaza/module-reports
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

## 4. Highlight features

### New advanced reports

Along with other reports which are already accessible and available in default Magento 2, Mageplaza Reports extension complements two more advanced reports:
* Sale by Location report
* Repeat customers rate report

While the Sale by location report helps store owners have an overview of sales in specific countries/regions, that of Repeat customer rate shows how your loyalty program is doing in a visual way.

These statistical data are also ready to be compared with that of the previous period on a daily, monthly or even yearly basis.

![new advanced reports](https://i.imgur.com/suhEKmX.png)

### Customizable report period & report comparison

Store admins can select a period in which statistical data is demonstrated. Mageplaza Reports extension allows you to show figures of:
* Today
* Yesterday
* Last 7 days
* Last 30 days
* This month
* Last month
* Custom range

With Custom Range, admins are able to select specific time for the report and will not be limited to any range of time.

![customizable report period & report comparison](https://i.imgur.com/AvP9AJD.png)


#### Report comparison

Moreover, statistic data can be compared with that of the previous period which includes:
* The previous month
* The previous year
* Custom range

With Custom Range, store admins can filter figures which belong to a specific period of time to compare with the initial ones.

### Line-chart reports

In Mageplaza Reports extension standard version; reports on repeat customer rate, transactions, total sales, average order value, tax, and shipping will be displayed as line charts. This provides store owners with a visual look at statistic data, making it easier to compare, follow and process figures. In every graph, the rates between numbers will also be shown with red standing for positive statistics and green indicating negative ones.

![line-chart reports](https://i.imgur.com/1DL4ks1.png)

### Flexible dashboard layouts

On the dashboard, store admins can drag and drop to move report areas and arrange figure sections with ease. Moreover, the report board’s size can be adjusted by pulling the double arrow. Let’s see the screen gif below:

![dashboard layouts](https://i.imgur.com/jzcfzrS.gif)


## 5. More features

### Friendly presentation

Figures and numbers can be a headache to deal with. Thank to the extension, you can now view them in forms of charts and graphs, making them much easier to follow.

### Rates & Changes

A great thing about Reports is its instant analysis. With just a quick glance, you can see how your current performance compared to that of previous periods. 

### Enhance default reports

Mageplaza Reports better the default reports that come with Magento2. It adds further analysis of Revenue, Tax, Shipping and Quantity.

### High compatibility

This extension is compatible with many other Mageplaza products, such as Reward points, Gift card, Affiliate and etc. Once you have the others installed, their statistics will appear in the Reports as well.

## 6. Full-features list

- Enable/Disable module
- Enable store admins to show customized reports on Dashboard
- Enable store admins to compare figures in reports
- Two new advanced reports: Sale by location and Repeat customers rate reports
- Reports appear as line charts
- Reports in specified period
- Compare statistic data with previous period
- Enhance default reports on Tax, Revenue, Shipping and Quantity
- Highly informative Dashboard
- Drag and drop to arrange report layouts
- Adjust report boards’ sizes by pulling double arrows

## 7. How to configure Mageplaza Report extension

First, you need to enable the module. Login to Magento backend, then on the admin panel choose **Store > Settings > Configuration > Mageplaza Extensions > Reports**. 

![configuration](https://i.imgur.com/BkoGnd1.png)

Below, we will take a look at how Reports can be configured.

### General Configuration

![general configuration](https://i.imgur.com/IGClDwN.png)

* In the **Module Enable** field: Choose Yes to enable Reports extension.
* In the **Enable Chart** field: Select Yes to show the graph.
* In the **Enable Comparison** field: select Yes to display comparison of store performance between time periods.

### Quick report outside the Dashboard

After the initial configuration is completed, your dashboard will change with the addition of new graphs as shown in the following image:

![report outside the dashboard](https://i.imgur.com/EZl9a4Q.png)

In the Dashboard, you can:
* Remove and add report cards that suit your need.
* Rearrange the cards as you see fit and even adjust their size







