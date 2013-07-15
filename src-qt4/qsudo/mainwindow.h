#ifndef MAINWIN_H
#define MAINWIN_H

#include "ui_mainwindow.h"
#include <QProcess>
#include "../config.h"

class MainWindow : public QMainWindow, private Ui::MainWindow
{
        Q_OBJECT

public:
        MainWindow() : QMainWindow()
        {
          setupUi(this);
        }

public slots:
    void ProgramInit();

private slots:
    void slotButtonClicked(QAbstractButton *myBut);
    void slotReturnPressed();
    void slotProcDone();

private:
    void startSudo();
    void testPass();
    QProcess *sudoProc;
    int tries;

signals:

} ;
#endif // MAINWIN_H

