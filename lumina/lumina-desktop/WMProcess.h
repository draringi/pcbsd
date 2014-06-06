//===========================================
//  Lumina-DE source code
//  Copyright (c) 2012, Ken Moore
//  Available under the 3-clause BSD license
//  See the LICENSE file for full details
//===========================================
#ifndef _LUMINA_DESKTOP_WMPROCESS_H
#define _LUMINA_DESKTOP_WMPROCESS_H

#include <QProcess>
#include <QFile>
#include <QDir>
#include <QDebug>

class WMProcess : public QProcess{
	Q_OBJECT
public:
	WMProcess();
	~WMProcess();
	
	void startWM();
	void stopWM();
	
private:
	bool inShutdown;
	bool isRunning();
	QString setupWM();
	void cleanupConfig();
	QProcess *ssaver;
	
private slots:
	void processFinished(int exitcode, QProcess::ExitStatus status);

signals:
	void WMShutdown();
};

#endif

